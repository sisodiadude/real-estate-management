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
    public function create(string $branchSlug, Request $request)
    {
        // Verify if the user has the necessary permissions to create a department
        if (!$request->user->canPerform('Admin Department', 'create')) {
            abort(403, 'Access denied. You do not have the required permissions to create a department.');
        }

        // Retrieve the branch by slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            abort(404, 'Branch not found. Please verify the branch information and try again.');
        }

        // Load the department creation view with required data
        return view('admin.department.create', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'branch' => $branch,
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(string $branchSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        try {

            // Retrieve the branch by slug
            $branch = AdminBranch::where('slug', $branchSlug)->first();

            if (!$branch) {
                abort(404, 'Branch not found. Please verify the branch information and try again.');
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
                'email' => 'required|email|max:255|unique:admin_departments,email',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:admin_departments,mobile',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',

                // Use Branch Operating Hours (Required Boolean)
                'use_branch_operating_hours' => 'required|boolean',
            ];

            // Conditionally require operating_hours if use_branch_operating_hours is true
            $validationRules['operating_hours'] = $request->boolean('use_branch_operating_hours')
                ? 'required|json'
                : 'nullable|json';

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Department Name
                'name.required' => 'Department name is required.',
                'name.string' => 'Department name must be a valid string.',
                'name.max' => 'Department name cannot exceed 100 characters.',
                'name.unique' => 'A department with this name already exists.',

                // Email
                'email.required' => 'Department email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another department.',

                // Mobile
                'mobile.required' => 'Department mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another department.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Use Branch Operating Hours
                'use_branch_operating_hours.required' => 'Branch Operating Hours selection is required.',
                'use_branch_operating_hours.boolean' => 'Branch Operating Hours must be true or false.',

                // Operating Hours
                'operating_hours.required' => 'Operating hours are required when using branch Operating Hours.',
                'operating_hours.json' => 'Operating hours must be a valid JSON format.',

                // Department Status
                'status.required' => 'Department status is required.',
                'status.in' => 'Invalid department status selected. Valid options: active, inactive, suspended, archived.',
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
                        $request->latitude,
                        $request->longitude,
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
            $validatedData['branch_id'] = $branch->id;

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
                'redirect_url' => route('admin.branches.show', ['branchSlug' => $branch->slug]) // Replace 'department.list' with your actual route name
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

    public function getDepartments(string $branchSlug, Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'error' => 'Invalid request.'], 400);
        }

        if (!$request->user->canPerform('Admin Department', 'view_all')) {
            return response()->json(['status' => false, 'error' => 'You do not have permission to view branches.'], 403);
        }

        $branch = AdminBranch::where('slug', $branchSlug)->first();
        if (!$branch) {
            return response()->json(['status' => false, 'message' => 'Branch not found. Please verify and try again.'], 404);
        }

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));
        $searchValue = $request->input('search.value', '');
        $status = $request->input('status', '');
        $created_by = (int) $request->input('created_by', 0);
        $created_by_role = $request->input('created_by_role', '');
        $updated_by = (int) $request->input('updated_by', 0);
        $updated_by_role = $request->input('updated_by_role', '');
        $leader = (int) $request->input('leader', 0);

        $columns = ['department_unique_id', 'name', 'mobile', 'email', 'status', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        $query = AdminDepartment::byBranchID($branch->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($status);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('department_unique_id', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%")
                    ->orWhere('mobile', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        // Apply created by filter
        if ($created_by > 0 && $request->filled('created_by_role')) {
            $query->where('created_by_id', $created_by)
                ->where('created_by_type', $created_by_role === 'admins' ? 'App\Models\Admin' : 'App\Models\AdminEmployee');
        }

        // Apply updated by filter
        if ($updated_by > 0 && $request->filled('updated_by_role')) {
            $query->where('last_updated_by_id', $updated_by)
                ->where('last_updated_by_type', $updated_by_role === 'admins' ? 'App\Models\Admin' : 'App\Models\AdminEmployee');
        }

        // Apply leader filter
        if ($leader > 0) {
            $query->where('leader_id', $leader);
        }

        if ($request->filled('fromDate')) {
            $toDate = $request->input('toDate', now()->toDateString());
            $query->whereBetween('date_of_joining', [$request->input('fromDate'), $toDate]);
        }

        $totalRecords = AdminDepartment::byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch) {
                $getFullName = fn($first, $last) => trim("{$first} {$last}") ?: 'N/A';

                return [
                    'department_unique_id' => $row->department_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'status' => $row->status,
                    'leader' => $getFullName(optional($row->leader)->first_name, optional($row->leader)->last_name),
                    'creator' => $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name),
                    'updator' => $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name),
                    'created_at' => $row->created_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'updated_at' => $row->updated_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'actions' => [
                        'view' => $request->user->canPerform('Admin Department', 'view') ? route('admin.branches.departments.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null,
                        'edit' => $request->user->canPerform('Admin Department', 'edit') ? route('admin.branches.departments.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null,
                        'delete' => $request->user->canPerform('Admin Department', 'soft_delete') ? route('admin.branches.departments.delete', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null
                    ]
                ];
            });

        return response()->json([
            "draw" => (int) $request->input('draw', 0),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
    }

    public function edit(string $branchSlug, string $departmentSlug, Request $request)
    {
        try {
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

            return view('admin.department.edit', [
                'branch' => $branch,
                'department' => $department,
                'user' => $request->user,
                'userType' => $request->userType,
                'hasPermissions' => $request->user->permissions,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error editing department: ' . $e->getMessage());

            // Return a professional error response
            return abort(500, $e->getMessage() ?: 'An unexpected error occurred while loading the department details. Please try again.');
        }
    }

    /**
     * Store a newly created department.
     */
    public function update(string $branchSlug, string $departmentSlug, Request $request)
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
            if (!$request->user->canPerform('Admin Department', 'edit')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'Update',
                        false,
                        'Failed to update a department.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to update department']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminDepartment::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to update departments.',
                ], 403);
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_departments,name,' . $department->id,
                'email' => 'required|email|max:255|unique:admin_departments,email,' . $department->id,
                'mobile' => [
                    'required',
                    'string',
                    'max:20,' . $department->id . 'unique:admin_departments,mobile',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',

                // Use Branch SMTP Credentials (Required Boolean)
                'use_branch_operating_hours' => 'required|boolean',
            ];

            // Conditionally require operating_hours if use_branch_operating_hours is true
            $validationRules['operating_hours'] = $request->boolean('use_branch_operating_hours')
                ? 'required|json'
                : 'nullable|json';

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Department Name
                'name.required' => 'Department name is required.',
                'name.string' => 'Department name must be a valid string.',
                'name.max' => 'Department name cannot exceed 100 characters.',
                'name.unique' => 'A department with this name already exists.',

                // Email
                'email.required' => 'Department email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another department.',

                // Mobile
                'mobile.required' => 'Department mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another department.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Use Branch Operating Hours
                'use_branch_operating_hours.required' => 'Branch Operating Hours selection is required.',
                'use_branch_operating_hours.boolean' => 'Branch Operating Hours must be true or false.',

                // Operating Hours
                'operating_hours.required' => 'Operating hours are required when using branch Operating Hours.',
                'operating_hours.json' => 'Operating hours must be a valid JSON format.',

                // Department Status
                'status.required' => 'Department status is required.',
                'status.in' => 'Invalid department status selected. Valid options: active, inactive, suspended, archived.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'update',
                        false,
                        'Failed to update a department.',
                        $request->latitude,
                        $request->longitude,
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

            // Begin transaction
            DB::beginTransaction();

            // Store old values before updating
            $oldValues = $department->getOriginal();

            // Update branch data
            $department->update($validatedData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Department updated successfully', ['department_id' => $department->id, 'name' => $department->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Update',
                    true,
                    'A department has been successfully updated.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Department updation activity recorded.']), // Description field updated
                    [
                        'old' => $oldValues, // Get original values before update
                        'new' => $department->toArray()       // Get updated values after update
                    ],
                    AdminDepartment::class
                );
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Department updated successfully. Redirecting to the list.',
                'redirect_url' => route('admin.branches.show', ['branchSlug' => $branch->slug]) // Replace 'department.list' with your actual route name
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
                    'Update',
                    false,
                    'Failed to update a department.',
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

    public function delete(string $branchSlug, string $departmentSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();

        if (!$branch || !$department) {
            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: The requested branch or department does not exist.',
            ], 404);
        }

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
            if (!$request->user->canPerform('Admin Department', 'soft_delete')) {
                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Soft Delete',
                        true, // Assuming the soft delete was successful
                        'The department has been successfully soft deleted.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to soft delete department']),
                        $department->toArray(), // Exclude latitude & longitude if necessary
                        AdminDepartment::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Soft delete the branch
            $department->delete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Soft Delete',
                    true, // Assuming the soft delete was successful
                    'The department has been successfully soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Soft deletion completed']),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The department has been successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Department deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Soft Delete',
                    false, // Assuming the soft delete was successful
                    'The department has been failed soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTrashedDepartments(string $branchSlug, Request $request)
    {
        // Ensure the request is an AJAX request
        if (!$request->ajax()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid request.'
            ], 400);
        }

        // Check user permissions
        if (!$request->user->canPerform('Admin Department', 'view_all_trashed')) {
            return response()->json([
                'status' => false,
                'error' => 'You do not have permission to view trashed branches.'
            ], 403);
        }

        // Retrieve branch by slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found. Please verify the branch information and try again.'
            ], 404);
        }

        // DataTables parameters
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));
        $searchValue = $request->input('search.value', '');
        $deletedBy = (int) $request->input('deleted_by', 0);
        $deletedByRole = $request->input('deleted_by_role', '');
        $leader = (int) $request->input('leader', 0);
        $fromDateFilter = $request->input('fromDate');
        $toDateFilter = $request->input('toDate', now()->toDateString());

        // Columns available for ordering
        $columns = ['department_unique_id', 'name', 'mobile', 'email', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        // Fetch trashed departments
        $query = AdminDepartment::onlyTrashed()->byBranchID($branch->id);

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('department_unique_id', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%")
                    ->orWhere('mobile', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        if ($deletedBy > 0 && $deletedByRole) {
            $query->where('last_updated_by_id', $deletedBy)
                ->where('last_updated_by_type', $deletedByRole === 'admins' ? 'App\Models\Admin' : 'App\Models\AdminEmployee');
        }

        if ($leader > 0) {
            $query->where('leader_id', $leader);
        }

        if ($fromDateFilter) {
            $query->whereBetween('date_of_joining', [$fromDateFilter, $toDateFilter]);
        }

        // Get total and filtered record count
        $totalRecords = AdminDepartment::onlyTrashed()->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch) {
                $getFullName = fn($firstName, $lastName) => trim("{$firstName} {$lastName}") ?: 'N/A';

                return [
                    'department_unique_id' => $row->department_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'leader' => $getFullName(optional($row->leader())->first_name, optional($row->leader())->last_name),
                    'creator' => $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name),
                    'deletor' => $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name),
                    'created_at' => $row->created_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'deleted_at' => $row->updated_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'actions' => [
                        'restore' => $request->user->canPerform('Admin Department', 'restore_trashed')
                            ? route('admin.branches.departments.restore', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug])
                            : null,
                        'delete' => $request->user->canPerform('Admin Department', 'permanent_delete')
                            ? route('admin.branches.departments.destroy', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug])
                            : null
                    ]
                ];
            });

        // Return JSON response
        return response()->json([
            "draw" => (int) $request->input('draw', 0),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
    }

    public function restore(string $branchSlug, string $departmentSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::onlyTrashed()->where('slug', $departmentSlug)->first();

        if (!$branch || !$department) {
            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: The requested branch or department does not exist.',
            ], 404);
        }

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
            if (!$request->user->canPerform('Admin Department', 'restore_trashed')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'Restore',
                        true, // Assuming the soft delete was successful
                        'The department has been successfully restore.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to restore department']),
                        $department->toArray(), // Exclude latitude & longitude if necessary
                        AdminDepartment::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Ensure the department is actually deleted before attempting to restore
            if (!$department->trashed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This department is already active and does not need restoration.',
                ], 400);
            }

            // Restore the department
            $department->restore();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Restore',
                    true, // Assuming the soft delete was successful
                    'The department has been successfully restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Restored completed']),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Department restored successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Department restoration failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Restore',
                    false, // Assuming the soft delete was successful
                    'The department has been failed restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function destroy(string $branchSlug, string $departmentSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::onlyTrashed()->where('slug', $departmentSlug)->first();

        if (!$branch || !$department) {

            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: The requested branch or department does not exist.',
            ], 404);
        }

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
            if (!$request->user->canPerform('Admin Department', 'permanent_delete')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Department',
                        'Force Delete',
                        true, // Assuming the soft delete was successful
                        'The department has been failed force delete.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to force deleted']),
                        $department->toArray(), // Exclude latitude & longitude if necessary
                        AdminDepartment::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Permanently delete the department
            $department->forceDelete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Force Delete',
                    true, // Assuming the soft delete was successful
                    'The department has been successfully force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Force deleted completed']),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The department has been permanently deleted and cannot be recovered.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Department deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Department',
                    'Force Delete',
                    false, // Assuming the soft delete was successful
                    'The department has been failed force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $department->toArray(), // Exclude latitude & longitude if necessary
                    AdminDepartment::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function show(string $branchSlug, string $departmentSlug, Request $request)
    {
        try {
            // Check user permissions
            if (!$request->user->canPerform('Admin Department', 'view')) {
                abort(403, 'You do not have permission to view department.');
            }

            // Fetch the branch and department in a single query to optimize performance
            $branch = AdminBranch::where('slug', $branchSlug)->first();
            $department = AdminDepartment::where('slug', $departmentSlug)->first();

            if (!$branch || !$department) {
                abort(404, 'Resource Not Found: The requested branch or department does not exist.');
            }

            // Return the view with required data
            return view('admin.department.show', [
                'branch' => $branch,
                'department' => $department,
                'user' => $request->user,
                'userType' => $request->userType,
                'hasPermissions' => $request->user->permissions,
                'userGroups' => ["admins" => Admin::orderBy('first_name', 'asc')->get()],
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error in Department Show: " . $e->getMessage(), [
                'branchSlug' => $branchSlug,
                'departmentSlug' => $departmentSlug,
                'user_id' => $request->user->id ?? null
            ]);

            // Corrected abort statement
            abort(500, $e->getMessage() ?? 'An unexpected error occurred. Please try again later.');
        }
    }
}
