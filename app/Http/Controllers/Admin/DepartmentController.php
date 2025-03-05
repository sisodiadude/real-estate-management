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
    public function create($branchSlug, Request $request)
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
    public function store($branchSlug, Request $request)
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

    public function getDepartments($branchSlug, Request $request)
    {

        // Ensure the request is an AJAX request
        if (!$request->ajax()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid request.'
            ], 400);
        }

        // Ensure the user has permission to view branches
        if (!$request->user->canPerform('Admin Department', 'view_all')) {
            return response()->json([
                'status' => false,
                'error' => 'You do not have permission to view branches.'
            ], 403);
        }

        // Retrieve the branch by slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found. Please verify the branch information and try again.'
            ], 404);
        }

        // Pagination and ordering parameters from DataTables
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        // echo "$orderColumnIndex <br>";
        // die;
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));
        $searchValue = $request->input('search.value', '');
        $created_by = (int) $request->input('created_by', 0);
        $created_by_role = $request->input('created_by_role', '');
        $updated_by = (int) $request->input('updated_by', 0);
        $updated_by_role = $request->input('updated_by_role', '');
        $leader = (int) $request->input('leader', 0);
        $fromDateFilter = $request->input('fromDate');
        $toDateFilter = $request->input('toDate');

        // Columns available for ordering
        $columns = ['department_unique_id', 'name', 'mobile', 'email', 'created_at', 'updated_at'];

        // Ensure a valid column is selected for ordering
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        // Fetch branches with filtering
        $query = AdminDepartment::query()->byStatus('active')->byBranchID($branch->id);

        // Apply search filter
        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
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

        // Apply date range filter
        if ($request->filled('fromDate')) {
            $toDateFilter = $toDateFilter ?? now()->toDateString();
            $query->whereBetween('date_of_joining', [$fromDateFilter, $toDateFilter]);
        }

        // Get total and filtered record count
        $totalRecords = AdminDepartment::byStatus('active')->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch) {

                // Helper function to format names
                $getFullName = fn($firstName, $lastName) => trim("{$firstName} {$lastName}") ?: 'N/A';

                $leader = $getFullName(optional($row->leader())->first_name, optional($row->leader())->last_name);
                $creator = $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name);
                $updator = $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name);

                // Checking permissions for actions
                $viewEdit = $request->user->canPerform('Admin Department', 'view');
                $canEdit = $request->user->canPerform('Admin Department', 'edit');
                $canDelete = $request->user->canPerform('Admin Department', 'soft_delete');

                // Generate URLs for edit and delete actions
                $viewUrl = $viewEdit ? route('admin.departments.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null;
                $editUrl = $canEdit ? route('admin.departments.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null;
                $deleteUrl = $canDelete ? route('admin.departments.delete', ['branchSlug' => $branch->slug, 'departmentSlug' => $row->slug]) : null;

                $timezone = $request->user->country->timezones[0]['zoneName'] ?? 'UTC';

                return [
                    'department_unique_id' => $row->department_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'leader' => $leader,
                    'creator' => $creator,
                    'updator' => $updator,
                    'created_at' => $row->created_at->setTimezone($timezone)->format('Y-m-d H:i:s'),
                    'updated_at' => $row->updated_at->setTimezone($timezone)->format('Y-m-d H:i:s'),
                    'actions' => [
                        'view' => $viewUrl,
                        'edit' => $editUrl,
                        'delete' => $deleteUrl
                    ]
                ];
            });

        // Prepare JSON response
        return response()->json([
            "draw" => (int) $request->input('draw', 0),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
    }
}
