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
            return abort(403, 'Access Denied: You do not have permission to edit teams.');
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

    public function getTeams(string $branchSlug, string $departmentSlug, Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'error' => 'Invalid request.'], 400);
        }

        if (!$request->user->canPerform('Admin Team', 'view_all')) {
            return response()->json(['status' => false, 'error' => 'You do not have permission to view teams.'], 403);
        }

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();

        if (!$branch || !$department) {
            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: The requested branch or department does not exist.',
            ], 404);
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

        $columns = ['team_unique_id', 'name', 'mobile', 'email', 'status', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        $query = AdminTeam::byDepartmentID($department->id)->byBranchID($branch->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($status);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('team_unique_id', 'like', "%{$searchValue}%")
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

        $totalRecords = AdminTeam::byDepartmentID($department->id)->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch, $department) {
                $getFullName = fn($first, $last) => trim("{$first} {$last}") ?: 'N/A';

                return [
                    'team_unique_id' => $row->team_unique_id,
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
                        'view' => $request->user->canPerform('Admin Team', 'view') ? route('admin.teams.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $row->slug]) : null,
                        'edit' => $request->user->canPerform('Admin Team', 'edit') ? route('admin.teams.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $row->slug]) : null,
                        'delete' => $request->user->canPerform('Admin Team', 'soft_delete') ? route('admin.teams.delete', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $row->slug]) : null
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

    public function edit(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        try {
            // Check user permissions
            if (!$request->user->canPerform('Admin Department', 'edit')) {
                return abort(403, 'Access Denied: You do not have permission to edit departments.');
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

            return view('admin.team.edit', [
                'branch' => $branch,
                'department' => $department,
                'team' => $team,
                'user' => $request->user,
                'userType' => $request->userType,
                'hasPermissions' => $request->user->permissions,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error editing team: ' . $e->getMessage());

            // Return a professional error response
            return abort(500, $e->getMessage() ?: 'An unexpected error occurred while loading the team details. Please try again.');
        }
    }

    public function update(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
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
                return response()->json([
                    'status' => false,
                    'message' => 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.',
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
            if (!$request->user->canPerform('Admin Team', 'edit')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Team',
                        'Update',
                        false,
                        'Failed to update a new team.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to update team']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminTeam::class
                    );
                }
                abort(403, 'You do not have permission to update team.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_teams,name,' . $department->id,
                'email' => 'required|email|max:255|unique:admin_teams,email,' . $department->id,
                'mobile' => [
                    'required',
                    'string',
                    'max:20,' . $department->id . 'unique:admin_teams,mobile',
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
                        'Update',
                        false,
                        'Failed to update a new team.',
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

            // Begin transaction
            DB::beginTransaction();

            // Store old values before updating
            $oldValues = $team->getOriginal();

            // Update branch data
            $team->update($validatedData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Team updated successfully', ['team_id' => $team->id, 'name' => $team->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Update',
                    true,
                    'A new team has been successfully updated.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Team creation activity recorded.']), // Description field updated
                    [
                        'old' => $oldValues, // Get original values before update
                        'new' => $department->toArray()       // Get updated values after update
                    ],
                    AdminTeam::class
                );
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Team updated successfully. Redirecting to the list.',
                'redirect_url' => route('admin.departments.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug]) // Replace 'team.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Team updation failed', [
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
                'message' => $e->getMessage() ?: 'An error occurred while creating the team.'
            ], 500);
        }
    }

    public function delete(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

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
            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.',
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
            if (!$request->user->canPerform('Admin Team', 'soft_delete')) {
                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Team',
                        'Soft Delete',
                        true, // Assuming the soft delete was successful
                        'The team has been successfully soft deleted.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to soft delete team']),
                        $team->toArray(), // Exclude latitude & longitude if necessary
                        AdminTeam::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Soft delete the branch
            $team->delete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Soft Delete',
                    true, // Assuming the soft delete was successful
                    'The team has been successfully soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Soft deletion completed']),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The team has been successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Team deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Soft Delete',
                    false, // Assuming the soft delete was successful
                    'The team has been failed soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTrashedTeams(string $branchSlug, string $departmentSlug, Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'error' => 'Invalid request.'], 400);
        }

        if (!$request->user->canPerform('Admin Team', 'view_all')) {
            return response()->json(['status' => false, 'error' => 'You do not have permission to view teams.'], 403);
        }

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";

        // If any resource is missing, return a detailed 404 error
        if (!empty($missing)) {
            return response()->json([
                'status' => false,
                'message' => 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.',
            ], 404);
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

        $columns = ['team_unique_id', 'name', 'mobile', 'email', 'status', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        $query = AdminTeam::onlyTrashed()->byDepartmentID($department->id)->byBranchID($branch->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($status);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('team_unique_id', 'like', "%{$searchValue}%")
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

        $totalRecords = AdminTeam::onlyTrashed()->byDepartmentID($department->id)->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch, $department) {
                $getFullName = fn($first, $last) => trim("{$first} {$last}") ?: 'N/A';

                return [
                    'team_unique_id' => $row->team_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'status' => $row->status,
                    'leader' => $getFullName(optional($row->leader)->first_name, optional($row->leader)->last_name),
                    'creator' => $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name),
                    'deletor' => $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name),
                    'created_at' => $row->created_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'deleted_at' => $row->updated_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'actions' => [
                        'restore' => $request->user->canPerform('Admin Team', 'restore_trashed') ? route('admin.teams.restore', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $row->slug]) : null,
                        'delete' => $request->user->canPerform('Admin Team', 'permanent_delete') ? route('admin.teams.destroy', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $row->slug]) : null
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

    public function restore(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::onlyTrashed()->where('slug', $teamSlug)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Team (Slug: $teamSlug)";

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
            if (!$request->user->canPerform('Admin Team', 'restore_trashed')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Team',
                        'Restore',
                        true, // Assuming the soft delete was successful
                        'The team has been successfully restore.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to restore team']),
                        $team->toArray(), // Exclude latitude & longitude if necessary
                        AdminTeam::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Ensure the team is actually deleted before attempting to restore
            if (!$team->trashed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This team is already active and does not need restoration.',
                ], 400);
            }

            // Restore the team
            $team->restore();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Restore',
                    true, // Assuming the soft delete was successful
                    'The team has been successfully restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Restored completed']),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Team restored successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Team restoration failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Restore',
                    false, // Assuming the soft delete was successful
                    'The team has been failed restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function destroy(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::onlyTrashed()->where('slug', $teamSlug)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Team (Slug: $teamSlug)";

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
            if (!$request->user->canPerform('Admin Team', 'permanent_delete')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Team',
                        'Force Delete',
                        true, // Assuming the soft delete was successful
                        'The team has been failed force delete.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to force deleted']),
                        $team->toArray(), // Exclude latitude & longitude if necessary
                        AdminTeam::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Permanently delete the team
            $team->forceDelete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Force Delete',
                    true, // Assuming the soft delete was successful
                    'The team has been successfully force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Force deleted completed']),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The team has been permanently deleted and cannot be recovered.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Team deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Team',
                    'Force Delete',
                    false, // Assuming the soft delete was successful
                    'The team has been failed force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $team->toArray(), // Exclude latitude & longitude if necessary
                    AdminTeam::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function show(string $branchSlug, string $departmentSlug, string $teamSlug, Request $request)
    {
        try {
            // Check user permissions
            if (!$request->user->canPerform('Admin Team', 'view')) {
                abort(403, 'You do not have permission to view team.');
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
                abort(404,  'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.');
            }

            // Return the view with required data
            return view('admin.team.show', [
                'branch' => $branch,
                'department' => $department,
                'team' => $team,
                'user' => $request->user,
                'userType' => $request->userType,
                'hasPermissions' => $request->user->permissions,
                'userGroups' => ["admins" => Admin::orderBy('first_name', 'asc')->get()],
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error in Team Show: " . $e->getMessage(), [
                'branchSlug' => $branchSlug,
                'departmentSlug' => $departmentSlug,
                'teamSlug' => $teamSlug,
                'user_id' => $request->user->id ?? null
            ]);

            // Corrected abort statement
            abort(500, $e->getMessage() ?? 'An unexpected error occurred. Please try again later.');
        }
    }
}
