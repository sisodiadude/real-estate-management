<?php

namespace App\Http\Controllers\Admin;

// Base Controller
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;
// Models
use App\Models\City;
use App\Models\State;
use App\Models\Country;
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

class DepartmentController extends Controller
{
    public function __call($method, $parameters)
    {
        // Check if the request expects a JSON response
        if (request()->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => "The method '$method' does not exist in " . static::class,
            ], Response::HTTP_NOT_FOUND);
        }

        // If it's not an API request, abort with a 404 error
        abort(Response::HTTP_NOT_FOUND, "Method $method does not exist.");
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(Request $request)
    {
        // Check if the user has permission to create a new department
        if (!$request->user->canPerform('Admin Department', 'create')) {
            abort(403, 'You do not have permission to create a department.');
        }

        // Return the view for department creation with necessary data
        return view('admin.department.create', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        try {
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
            if (!$request->user->canPerform('Admin Department', 'create')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'Create',
                        false,
                        'Failed to create a new department.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to create department']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminDepartment::class
                    );
                }
                abort(403, 'You do not have permission to create departments.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_departments,name',
                'email' => 'nullable|email|max:255|unique:admin_departments,email',
                'mobile' => 'nullable|string|max:20|unique:admin_departments,mobile|regex:/^\+?[0-9\s-]{10,20}$/',
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string',

                // Operating Hours (JSON)
                'operating_hours' => 'nullable|json',
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Department Name
                'name.required' => 'Department name is required.',
                'name.string' => 'Department name must be a valid string.',
                'name.max' => 'Department name cannot exceed 100 characters.',
                'name.unique' => 'A Department with this name already exists.',

                // Email
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another department.',

                // Mobile
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another department.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Operating Hours
                'operating_hours.json' => 'Operating hours must be a valid JSON format.',

                // Department Status
                'status.required' => 'Department status is required.',
                'status.in' => 'Invalid Department status selected.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'Create',
                        false,
                        'Failed to create a new department.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode($validator->errors()->toArray()),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminDepartment::class
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

            if (isset($validatedData['social_links']) && json_decode($validatedData['social_links']) === []) {
                $validatedData['social_links'] = null;
            }

            // Parse tax data if present
            $taxDetails = [];
            if (!empty($validatedData['tax_details'])) {
                $taxDataArray = json_decode($validatedData['tax_details'], true);
                if (is_array($taxDataArray)) {
                    foreach ($taxDataArray as $tax) {
                        if (!empty($tax['title']) && isset($tax['percentage'])) {
                            $taxDetails[] = [
                                'title' => $tax['title'],
                                'percentage' => $tax['percentage'],
                            ];
                        }
                    }
                }
            }

            $validatedData['tax_details'] = json_encode($taxDetails);

            // Begin transaction
            DB::beginTransaction();

            // Create and store the department
            $department = AdminDepartment::create($validatedData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Department created successfully', ['department_id' => $department->id, 'name' => $department->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Create',
                    true,
                    'A new department has been successfully created.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Department creation activity recorded.']), // Description field updated
                    $department->toArray(),
                    AdminDepartment::class
                );
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Department created successfully. Redirecting to the list.',
                'redirect_url' => route('admin.departments.index') // Replace 'department.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Department creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Create',
                    false,
                    'Failed to create a new department.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => $e->getMessage()]),
                    collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                    AdminDepartment::class
                );
            }

            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the department.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
