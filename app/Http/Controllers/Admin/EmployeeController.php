<?php

namespace App\Http\Controllers\Admin;

// Base Controller
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;

// Models
use App\Models\Country;
use App\Models\AdminTeam;
use App\Models\AdminBranch;
use App\Models\AdminDepartment;

// Laravel Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// HTTP Response Handling
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function create(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        // Check user permissions
        if (!$request->user->canPerform('Admin Employee', 'create')) {
            return abort(403, 'Access Denied: You do not have permission to create employee.');
        }

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::where('slug', $teamSlug)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Team (Slug: $teamSlug)";

        // If any resource is missing, return a detailed 404 error
        if (!empty($missing)) {
            return abort(404, 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.');
        }

        // Load the department creation view with required data
        return view('admin.employee.create', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'branch' => $branch,
            'department' => $department,
            'team' => $team,
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }

    public function store(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        try {

            // Fetch the branch and department in a single query to optimize performance
            $branch = AdminBranch::where('slug', $branchSlug)->first();
            $department = AdminDepartment::where('slug', $departmentSlug)->first();
            $team = AdminTeam::where('slug', $teamSlug)->first();

            // Determine which resource is missing
            $missing = [];
            if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
            if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
            if (!$team) $missing[] = "Team (Slug: $teamSlug)";

            // If any resource is missing, return a detailed 404 error
            if (!empty($missing)) {
                return abort(404, 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.');
            }

            // Validate latitude and longitude
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            if (
                !is_numeric($latitude) || !is_numeric($longitude) ||
                $latitude < -90 || $latitude > 90 ||
                $longitude < -180 || $longitude > 180
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Location access is required. Please enable GPS or allow location permissions and try again.',
                ], 422);
            }

            // Check user permissions
            if (!$request->user->canPerform('Admin Employee', 'create')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Create',
                        false,
                        'Failed to create a new employee.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to create team']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminTeam::class
                    );
                }
                abort(403, 'You do not have permission to create employee.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:admin_employees,email',
                'alternative_email' => 'required|email|max:255',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:admin_employees,mobile',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'alternate_mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'dob' => 'required|date|before:today|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'marital_status' => 'nullable|in:single,married,divorced,widowed',
                'nationality_id' => 'required|integer|exists:countries,id',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',

                // Current Address
                'current_address_line1' => 'required|string|max:255',
                'current_address_line2' => 'nullable|string|max:500',
                'current_country_id' => 'required|integer|exists:countries,id',
                'current_state_id' => 'required|integer|exists:states,id',
                'current_city_id' => 'required|integer|exists:cities,id',
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Team Name
                'name.required' => 'Team name is required.',
                'name.string' => 'Team name must be a valid string.',
                'name.max' => 'Team name cannot exceed 100 characters.',

                // Email
                'email.required' => 'Team email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another team.',

                // Alternative Email
                'alternative_email.required' => 'Employee alternative email is required.',
                'alternative_email.email' => 'Please enter a valid alternative email address.',
                'alternative_email.max' => 'Alternative email address cannot exceed 255 characters.',

                // Mobile
                'mobile.required' => 'Team mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another team.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Mobile
                'alternate_mobile.required' => 'Employee alternate mobile is required.',
                'alternate_mobile.string' => 'Alternate mobile number must be a valid string.',
                'alternate_mobile.max' => 'Alternate mobile number cannot exceed 20 characters.',
                'alternate_mobile.regex' => 'Please enter a valid alternate mobile number (e.g., +1234567890, 9876543210).',

                // Date of Birth
                'dob.required' => 'Date of birth is required.',
                'dob.date' => 'Please enter a valid date of birth.',
                'dob.before' => 'Date of birth must be before today.',
                'dob.before_or_equal' => 'You must be at least 18 years old.',

                // Marital Status
                'marital_status.in' => 'Please select a valid marital status (Single, Married, Divorced, Widowed).',

                // Nationality
                'nationality_id.required' => 'Nationality is required.',
                'nationality_id.integer' => 'Nationality must be a valid integer ID.',
                'nationality_id.exists' => 'The selected nationality does not exist in the system.',

                // Blood Group
                'blood_group.in' => 'Please select a valid blood group (A+, A-, B+, B-, O+, O-, AB+, AB-).',

                // Status
                'status.required' => 'Status is required.',
                'status.in' => 'Please select a valid status (Active, Inactive, Suspended, Archived).',

                // Description
                'description.string' => 'Description must be a valid string.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Current Address
                'current_address_line1.required' => 'Current address line 1 is required.',
                'current_address_line1.string' => 'Current address line 1 must be a valid string.',
                'current_address_line1.max' => 'Current address line 1 cannot exceed 255 characters.',

                'current_address_line2.string' => 'Current address line 2 must be a valid string.',
                'current_address_line2.max' => 'Current address line 2 cannot exceed 500 characters.',

                'current_country_id.required' => 'Current country is required.',
                'current_country_id.integer' => 'Current country must be a valid ID.',
                'current_country_id.exists' => 'The selected country does not exist in the system.',

                'current_state_id.required' => 'Current state is required.',
                'current_state_id.integer' => 'Current state must be a valid ID.',
                'current_state_id.exists' => 'The selected state does not exist in the system.',

                'current_city_id.required' => 'Current city is required.',
                'current_city_id.integer' => 'Current city must be a valid ID.',
                'current_city_id.exists' => 'The selected city does not exist in the system.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Create',
                        false,
                        'Failed to create a new employee.',
                        $request->latitude,
                        $request->longitude,
                        json_encode($validator->errors()->toArray()),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminTeam::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            // Extract validated data
            $validatedData = collect($validator->validated())->toArray();
            $validatedData['branch_id'] = $branch->id;
            $validatedData['department_id'] = $department->id;
            $validatedData['team_id'] = $team->id;
            prArr($validatedData, 1);
            // Begin transaction
            DB::beginTransaction();

            // Create and store the team
            $team = AdminTeam::create($validatedData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Team created successfully', ['team_id' => $team->id, 'name' => $team->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Create',
                    true,
                    'A new team has been successfully created.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Team creation activity recorded.']), // Description field updated
                    $team->toArray(),
                    AdminTeam::class
                );
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Team created successfully. Redirecting to the list.',
                'redirect_url' => route('admin.departments.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug]) // Replace 'team.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Team creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Create',
                    false,
                    'Failed to create a new team.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => $e->getMessage()]),
                    collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                    AdminTeam::class
                );
            }

            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the team.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
