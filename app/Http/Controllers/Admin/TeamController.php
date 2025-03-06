<?php

namespace App\Http\Controllers\Admin;

// Base Controller
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;

// Models
use App\Models\Country;
use App\Models\AdminBranch;
use App\Models\AdminDepartment;
use App\Models\AdminTeam;
// Laravel Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// HTTP Response Handling
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{
    /**
     * Show the form for creating a new department.
     */
    public function create(string $branchSlug, string $departmentSlug, Request $request)
    {
        // Check user permissions
        if (!$request->user->canPerform('Admin Department', 'edit')) {
            return abort(403, 'Access Denied: You do not have permission to edit departments.');
        }

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();

        if (!$branch || !$department) {
            return abort(404, 'Resource Not Found: The requested branch or department does not exist.');
        }

        // Load the department creation view with required data
        return view('admin.team.create', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'branch' => $branch,
            'department' => $department,
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(string $branchSlug, string $departmentSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        try {

            // Fetch the branch and department in a single query to optimize performance
            $branch = AdminBranch::where('slug', $branchSlug)->first();
            $department = AdminDepartment::where('slug', $departmentSlug)->first();

            if (!$branch || !$department) {
                return response()->json([
                    'status' => false,
                    'message' => 'Resource Not Found: The requested branch or department does not exist.',
                ], 404);
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
            if (!$request->user->canPerform('Admin Team', 'create')) {
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
                        json_encode(['final_message' => 'not authorized to create team']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminTeam::class
                    );
                }
                abort(403, 'You do not have permission to create team.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_teams,name',
                'email' => 'required|email|max:255|unique:admin_teams,email',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:admin_teams,mobile',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Team Name
                'name.required' => 'Team name is required.',
                'name.string' => 'Team name must be a valid string.',
                'name.max' => 'Team name cannot exceed 100 characters.',
                'name.unique' => 'A Team with this name already exists.',

                // Email
                'email.required' => 'Team email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another team.',

                // Mobile
                'mobile.required' => 'Team mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another team.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Team Status
                'status.required' => 'Team status is required.',
                'status.in' => 'Invalid team status selected. Valid options: active, inactive, suspended, archived.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Team',
                        'Create',
                        false,
                        'Failed to create a new team.',
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
