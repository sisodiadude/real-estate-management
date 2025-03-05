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

// Laravel Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// HTTP Response Handling
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BranchController extends Controller
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
     * Show the form for creating a new branch.
     */
    public function create(Request $request)
    {
        // Check if the user has permission to create a new branch
        if (!$request->user->canPerform('Admin Branch', 'Create')) {
            abort(403, 'You do not have permission to create a branch.');
        }

        // Return the view for branch creation with necessary data
        return view('admin.branch.create', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created branch.
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
            if (!$request->user->canPerform('Admin Branch', 'Create')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Create',
                        false,
                        'Failed to create a new branch.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to create branch']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                            ->merge([
                                'latitude' => $request->branch_latitude,
                                'longitude' => $request->branch_longitude
                            ])
                            ->toArray(), // Exclude latitude & longitude
                        AdminBranch::class
                    );
                }
                abort(403, 'You do not have permission to create branches.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_branches,name',
                'email' => 'required|email|max:255|unique:admin_branches,email',
                'mobile' => 'required|string|max:20|unique:admin_branches,mobile|regex:/^\+?[0-9\s-]{10,20}$/',
                'date_of_start' => 'nullable|date',
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|max:2048', // Max 2MB, accepts image files only
                'type' => 'required|in:head_office,regional,franchise,sub_branch',

                // Address & Location
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'postal_code' => 'required|string|max:10',

                // Geo Cordinates of branch
                'branch_latitude' => 'required|numeric|between:-90,90',
                'branch_longitude' => 'required|numeric|between:-180,180',

                // Operating Hours (JSON)
                'operating_hours' => 'nullable|json',

                // Tax & Compliance
                'gstin' => 'nullable|string|max:255|unique:admin_branches,gstin',
                'tax_details' => 'nullable|json',

                // Social Media Links (JSON)
                'social_links' => 'nullable|json',

                // SMTP Configuration (if enabled)
                'use_branch_smtp_credentials' => 'required|boolean',
            ];

            // Check if use_branch_smtp_credentials is true and add SMTP validation rules
            if ($request->input('use_branch_smtp_credentials') === true) {
                $validationRules = array_merge($validationRules, [
                    'smtp_host' => 'required|string|max:255',
                    'smtp_port' => 'required|integer|min:1|max:65535',
                    'smtp_username' => 'required|string|max:255',
                    'smtp_password' => 'required|string|max:255',
                    'smtp_encryption' => 'nullable|in:ssl,tls',
                    'smtp_from_email' => 'required|email|max:255',
                    'smtp_from_name' => 'required|string|max:255',
                ]);
            } else {
                // If use_branch_smtp_credentials is false or not present, make these fields nullable
                $validationRules = array_merge($validationRules, [
                    'smtp_host' => 'nullable|string|max:255',
                    'smtp_port' => 'nullable|integer|min:1|max:65535',
                    'smtp_username' => 'nullable|string|max:255',
                    'smtp_password' => 'nullable|string|max:255',
                    'smtp_encryption' => 'nullable|in:ssl,tls',
                    'smtp_from_email' => 'nullable|email|max:255',
                    'smtp_from_name' => 'nullable|string|max:255',
                ]);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Branch Name
                'name.required' => 'Branch name is required.',
                'name.string' => 'Branch name must be a valid string.',
                'name.max' => 'Branch name cannot exceed 100 characters.',
                'name.unique' => 'A branch with this name already exists.',

                // Email
                'email.required' => 'Branch email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another branch.',

                // Mobile
                'mobile.required' => 'Branch mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another branch.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Date of Start
                'date_of_start.date' => 'Please provide a valid date for the branch establishment.',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Address Fields
                'address_line1.required' => 'Primary address line is required.',
                'address_line1.string' => 'Primary address must be a valid string.',
                'address_line1.max' => 'Primary address cannot exceed 255 characters.',
                'address_line2.string' => 'Secondary address must be a valid string.',
                'address_line2.max' => 'Secondary address cannot exceed 255 characters.',

                // Country, State, City
                'country_id.required' => 'Country selection is required.',
                'country_id.exists' => 'Selected country is invalid.',
                'state_id.required' => 'State selection is required.',
                'state_id.exists' => 'Selected state is invalid.',
                'city_id.required' => 'City selection is required.',
                'city_id.exists' => 'Selected city is invalid.',

                // Postal Code
                'postal_code.required' => 'Postal code is required.',
                'postal_code.string' => 'Postal code must be a valid string.',
                'postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Geo Cordinates of branch
                'branch_latitude.required' => 'Location data is required to verify login activity.',
                'branch_latitude.numeric'  => 'Invalid location data. Latitude must be a number.',
                'branch_latitude.between'  => 'Latitude must be within the valid range (-90 to 90).',
                'branch_longitude.required' => 'Location data is required to verify login activity.',
                'branch_longitude.numeric' => 'Invalid location data. Longitude must be a number.',
                'branch_longitude.between' => 'Longitude must be within the valid range (-180 to 180).',

                // GSTIN
                'gstin.string' => 'GSTIN must be a valid string.',
                'gstin.unique' => 'This GSTIN is already in use.',

                // Branch Type
                'type.required' => 'Type is required.',
                'type.in' => 'Invalid type selected.',

                // Operating Hours
                'operating_hours.json' => 'Operating hours must be a valid JSON format.',

                // Social Links
                'social_links.json' => 'Social media links must be in a valid JSON format.',

                // SMTP Configuration
                'smtp_host.string' => 'SMTP Host must be a valid string.',
                'smtp_port.integer' => 'SMTP Port must be a valid number.',
                'smtp_username.string' => 'SMTP Username must be a valid string.',
                'smtp_password.string' => 'SMTP Password must be a valid string.',
                'smtp_encryption.in' => 'SMTP encryption must be either "ssl" or "tls".',
                'smtp_from_email.email' => 'Please provide a valid sender email address.',
                'smtp_from_name.string' => 'Sender name must be a valid string.',

                // Branch Status
                'status.required' => 'Branch status is required.',
                'status.in' => 'Invalid branch status selected.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Create',
                        false,
                        'Failed to create a new branch.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode($validator->errors()->toArray()),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                            ->merge([
                                'latitude' => $request->branch_latitude,
                                'longitude' => $request->branch_longitude
                            ])
                            ->toArray(), // Exclude latitude & longitude
                        AdminBranch::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            // Extract validated data
            $validatedData = collect($validator->validated())->except(['branch_latitude', 'branch_longitude'])->toArray();
            $validatedData['latitude'] = $request->branch_latitude;
            $validatedData['longitude'] = $request->branch_longitude;

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

            // Create and store the branch
            $branch = AdminBranch::create($validatedData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Branch created successfully', ['branch_id' => $branch->id, 'name' => $branch->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Create',
                    true,
                    'A new branch has been successfully created.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Branch creation activity recorded.']), // Description field updated
                    $branch->toArray(),
                    AdminBranch::class
                );
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Branch created successfully. Redirecting to the list.',
                'redirect_url' => route('admin.branches.index') // Replace 'branch.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Branch creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Create',
                    false,
                    'Failed to create a new branch.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => $e->getMessage()]),
                    collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                        ->merge([
                            'latitude' => $request->branch_latitude,
                            'longitude' => $request->branch_longitude
                        ])
                        ->toArray(), // Exclude latitude & longitude
                    AdminBranch::class
                );
            }

            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the list of branches.
     */
    public function index(Request $request)
    {
        // Check if the user has the required permission to view branches
        if (!$request->user->canPerform('Admin Branch', 'view_all')) {
            abort(403, 'You do not have permission to view branches.');
        }

        // Return the admin branch index view with required data
        return view('admin.branch.index', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'countries' => Country::orderBy('name', 'asc')->get(),
            'userGroups' => ["admins" => Admin::orderBy('first_name', 'asc')->get()],
        ]);
    }

    public function getBranches(Request $request)
    {

        // Ensure the request is an AJAX request
        if (!$request->ajax()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid request.'
            ], 400);
        }

        // Ensure the user has permission to view branches
        if (!$request->user->canPerform('Admin Branch', 'view_all')) {
            return response()->json([
                'status' => false,
                'error' => 'You do not have permission to view branches.'
            ], 403);
        }

        // Pagination and ordering parameters from DataTables
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        // echo "$orderColumnIndex <br>";
        // die;
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));
        $searchValue = $request->input('search.value', '');
        $country = (int) $request->input('country', 0);
        $state = (int) $request->input('state', 0);
        $city = (int) $request->input('city', 0);
        $created_by = (int) $request->input('created_by', 0);
        $created_by_role = $request->input('created_by_role', '');
        $updated_by = (int) $request->input('updated_by', 0);
        $updated_by_role = $request->input('updated_by_role', '');
        $leader = (int) $request->input('leader', 0);
        $fromDateFilter = $request->input('fromDate');
        $toDateFilter = $request->input('toDate');

        // Columns available for ordering
        $columns = ['branch_unique_id', 'name', 'mobile', 'email', 'created_at', 'updated_at'];

        // Ensure a valid column is selected for ordering
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        // Fetch branches with filtering
        $query = AdminBranch::query()->byStatus('active');

        // Apply search filter
        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('branch_unique_id', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%")
                    ->orWhere('mobile', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        // Apply country filter
        if ($country > 0) {
            $query->where('country_id', $country);
        }

        // Apply state filter
        if ($state > 0) {
            $query->where('state_id', $state);
        }

        // Apply city filter
        if ($city > 0) {
            $query->where('city_id', $city);
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
        $totalRecords = AdminBranch::byStatus('active')->count();
        $filteredRecords = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request) {

                // Helper function to format names
                $getFullName = fn($firstName, $lastName) => trim("{$firstName} {$lastName}") ?: 'N/A';

                $leader = $getFullName(optional($row->leader())->first_name, optional($row->leader())->last_name);
                $creator = $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name);
                $updator = $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name);

                // Checking permissions for actions
                $viewEdit = $request->user->canPerform('Admin Branch', 'view');
                $canEdit = $request->user->canPerform('Admin Branch', 'edit');
                $canDelete = $request->user->canPerform('Admin Branch', 'soft_delete');

                // Generate URLs for edit and delete actions
                $viewUrl = $viewEdit ? route('admin.branches.show', ['branchSlug' => $row->slug]) : null;
                $editUrl = $canEdit ? route('admin.branches.edit', ['branchSlug' => $row->slug]) : null;
                $deleteUrl = $canDelete ? route('admin.branches.delete', ['branchSlug' => $row->slug]) : null;

                $timezone = $request->user->country->timezones[0]['zoneName'] ?? 'UTC';

                return [
                    'branch_unique_id' => $row->branch_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'latitude' => $row->latitude,
                    'longitude' => $row->longitude,
                    'address' => $row->full_address,
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

    public function edit(string $branchSlug, Request $request)
    {
        // Check user permissions
        if (!$request->user->canPerform('Admin Branch', 'edit')) {
            abort(403, 'You do not have permission to edit branches.');
        }

        // Fetch the branch by slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            abort(404, 'The requested branch was not found.');
        }

        // Fetch states if country_id is not null
        $states = $branch->country_id ? State::where('country_id', $branch->country_id)->orderBy('name', 'asc')->get() : collect([]);

        // Fetch cities if state_id is not null
        $cities = $branch->state_id ? City::where('state_id', $branch->state_id)->orderBy('name', 'asc')->get() : collect([]);

        return view('admin.branch.edit', [
            'branch' => $branch,
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'countries' => Country::orderBy('name', 'asc')->get(),
            'states' => $states,
            'cities' => $cities,
        ]);
    }

    public function update(string $branchSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'error' => 'Branch not found.'
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
            if (!$request->user->canPerform('Admin Branch', 'Edit')) {
                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Update',
                        false,
                        'Failed to update a branch.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to update branch']),
                        ['old' => $branch->toArray(), 'new' => collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                            ->merge([
                                'latitude' => $request->branch_latitude,
                                'longitude' => $request->branch_longitude
                            ])
                            ->toArray()], // Exclude latitude & longitude
                        AdminBranch::class
                    );
                }
                abort(403, 'You do not have permission to edit branches.');
            }

            // Determine validation rules
            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_branches,name,' . $branch->id,
                'email' => 'nullable|email|max:255|unique:admin_branches,email,' . $branch->id,
                'mobile' => 'nullable|string|max:20|unique:admin_branches,mobile,' . $branch->id . '|regex:/^\+?[0-9\s-]{10,20}$/',
                'date_of_start' => 'nullable|date',
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|max:2048', // Max 2MB, accepts image files only
                'type' => 'required|in:head_office,regional,franchise,sub_branch',

                // Address & Location
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'postal_code' => 'required|string|max:10',

                // Geo Cordinates of branch
                'branch_latitude' => 'required|numeric|between:-90,90',
                'branch_longitude' => 'required|numeric|between:-180,180',

                // Operating Hours (JSON)
                'operating_hours' => 'nullable|json',

                // Tax & Compliance
                'gstin' => 'nullable|string|max:255|unique:admin_branches,gstin,' . $branch->id,
                'tax_details' => 'nullable|json',

                // Social Media Links (JSON)
                'social_links' => 'nullable|json',

                // SMTP Configuration (if enabled)
                'use_branch_smtp_credentials' => 'required|boolean',
            ];

            if (request()->input('use_branch_smtp_credentials')) {
                $validationRules = array_merge($validationRules, [
                    'smtp_host' => 'required|string|max:255',
                    'smtp_port' => 'required|integer|min:1|max:65535',
                    'smtp_username' => 'required|string|max:255',
                    'smtp_password' => 'required|string|max:255',
                    'smtp_encryption' => 'nullable|in:ssl,tls',
                    'smtp_from_email' => 'required|email|max:255',
                    'smtp_from_name' => 'required|string|max:255',
                ]);
            }

            // Check if use_branch_smtp_credentials is true and add SMTP validation rules
            if ($request->input('use_branch_smtp_credentials') === true) {
                $validationRules = array_merge($validationRules, [
                    'smtp_host' => 'required|string|max:255',
                    'smtp_port' => 'required|integer|min:1|max:65535',
                    'smtp_username' => 'required|string|max:255',
                    'smtp_password' => 'required|string|max:255',
                    'smtp_encryption' => 'nullable|in:ssl,tls',
                    'smtp_from_email' => 'required|email|max:255',
                    'smtp_from_name' => 'required|string|max:255',
                ]);
            } else {
                // If use_branch_smtp_credentials is false or not present, make these fields nullable
                $validationRules = array_merge($validationRules, [
                    'smtp_host' => 'nullable|string|max:255',
                    'smtp_port' => 'nullable|integer|min:1|max:65535',
                    'smtp_username' => 'nullable|string|max:255',
                    'smtp_password' => 'nullable|string|max:255',
                    'smtp_encryption' => 'nullable|in:ssl,tls',
                    'smtp_from_email' => 'nullable|email|max:255',
                    'smtp_from_name' => 'nullable|string|max:255',
                ]);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // Branch Name
                'name.required' => 'Branch name is required.',
                'name.string' => 'Branch name must be a valid string.',
                'name.max' => 'Branch name cannot exceed 100 characters.',
                'name.unique' => 'A branch with this name already exists.',

                // Email
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another branch.',

                // Mobile
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another branch.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Date of Start
                'date_of_start.date' => 'Please provide a valid date for the branch establishment.',

                // Description
                'description.string' => 'Description must be a valid text.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Address Fields
                'address_line1.required' => 'Primary address line is required.',
                'address_line1.string' => 'Primary address must be a valid string.',
                'address_line1.max' => 'Primary address cannot exceed 255 characters.',
                'address_line2.string' => 'Secondary address must be a valid string.',
                'address_line2.max' => 'Secondary address cannot exceed 255 characters.',

                // Country, State, City
                'country_id.required' => 'Country selection is required.',
                'country_id.exists' => 'Selected country is invalid.',
                'state_id.required' => 'State selection is required.',
                'state_id.exists' => 'Selected state is invalid.',
                'city_id.required' => 'City selection is required.',
                'city_id.exists' => 'Selected city is invalid.',

                // Postal Code
                'postal_code.required' => 'Postal code is required.',
                'postal_code.string' => 'Postal code must be a valid string.',
                'postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Geo Cordinates of branch
                'branch_latitude.required' => 'Location data is required to verify login activity.',
                'branch_latitude.numeric'  => 'Invalid location data. Latitude must be a number.',
                'branch_latitude.between'  => 'Latitude must be within the valid range (-90 to 90).',
                'branch_longitude.required' => 'Location data is required to verify login activity.',
                'branch_longitude.numeric' => 'Invalid location data. Longitude must be a number.',
                'branch_longitude.between' => 'Longitude must be within the valid range (-180 to 180).',

                // GSTIN
                'gstin.string' => 'GSTIN must be a valid string.',
                'gstin.unique' => 'This GSTIN is already in use.',

                // Branch Type
                'type.required' => 'Type is required.',
                'type.in' => 'Invalid type selected.',

                // Operating Hours
                'operating_hours.json' => 'Operating hours must be a valid JSON format.',

                // Social Links
                'social_links.json' => 'Social media links must be in a valid JSON format.',

                // SMTP Configuration
                'smtp_host.string' => 'SMTP Host must be a valid string.',
                'smtp_port.integer' => 'SMTP Port must be a valid number.',
                'smtp_username.string' => 'SMTP Username must be a valid string.',
                'smtp_password.string' => 'SMTP Password must be a valid string.',
                'smtp_encryption.in' => 'SMTP encryption must be either "ssl" or "tls".',
                'smtp_from_email.email' => 'Please provide a valid sender email address.',
                'smtp_from_name.string' => 'Sender name must be a valid string.',

                // Branch Status
                'status.required' => 'Branch status is required.',
                'status.in' => 'Invalid branch status selected.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {

                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Update',
                        false,
                        'Failed to update a branch.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode($validator->errors()->toArray()),
                        ['old' => $branch->toArray(), 'new' => collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                            ->merge([
                                'latitude' => $request->branch_latitude,
                                'longitude' => $request->branch_longitude
                            ])
                            ->toArray()], // Exclude latitude & longitude
                        AdminBranch::class
                    );
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $validatedData = collect($validator->validated())->except(['branch_latitude', 'branch_longitude'])->toArray();
            $validatedData['latitude'] = $request->branch_latitude;
            $validatedData['longitude'] = $request->branch_longitude;

            // Parse tax details
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

            // Start transaction
            DB::beginTransaction();

            // Store old values before updating
            $oldValues = $branch->getOriginal();

            // Update branch data
            $branch->update($validatedData);

            DB::commit();

            Log::info('Branch updated successfully', ['branch_id' => $branch->id, 'name' => $branch->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Update',
                    true,
                    'A branch has been successfully updated.',  // Removed "new" from message since it's an update
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Branch update activity recorded.']),
                    [
                        'old' => $oldValues, // Get original values before update
                        'new' => $branch->toArray()       // Get updated values after update
                    ],
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Branch updated successfully.',
                'redirect_url' => route('admin.branches.index')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Branch update failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Update',
                    false,
                    'Failed to update a branch.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($validator->errors()->toArray()),
                    ['old' => $branch->toArray(), 'new' => collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))
                        ->merge([
                            'latitude' => $request->branch_latitude,
                            'longitude' => $request->branch_longitude
                        ])
                        ->toArray()], // Exclude latitude & longitude
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(string $branchSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Find the branch by its slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'The requested branch could not be found.',
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
            if (!$request->user->canPerform('Admin Branch', 'soft_delete')) {
                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Soft Delete',
                        true, // Assuming the soft delete was successful
                        'The branch has been successfully soft deleted.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to soft delete branch']),
                        $branch->toArray(), // Exclude latitude & longitude if necessary
                        AdminBranch::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Soft delete the branch
            $branch->delete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Soft Delete',
                    true, // Assuming the soft delete was successful
                    'The branch has been successfully soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Soft deletion completed']),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The branch has been successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Branch deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Soft Delete',
                    false, // Assuming the soft delete was successful
                    'The branch has been failed soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }

    public function trash(Request $request)
    {
        // Check if the user has the required permission to view branches
        if (!$request->user->canPerform('Admin Branch', 'view_all_trashed')) {
            abort(403, 'You do not have permission to view trash branches.');
        }

        // Return the admin branch index view with required data
        return view('admin.branch.trash', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'countries' => Country::orderBy('name', 'asc')->get(),
            'userGroups' => ["admins" => Admin::orderBy('first_name', 'asc')->get()],
        ]);
    }

    public function getTrashedBranches(Request $request)
    {

        // Ensure the request is an AJAX request
        if (!$request->ajax()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid request.'
            ], 400);
        }

        // Ensure the user has permission to view branches
        if (!$request->user->canPerform('Admin Branch', 'view_all_trashed')) {
            return response()->json([
                'status' => false,
                'error' => 'You do not have permission to view trash branches.'
            ], 403);
        }

        // Pagination and ordering parameters from DataTables
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        // echo "$orderColumnIndex <br>";
        // die;
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));
        $searchValue = $request->input('search.value', '');
        $country = (int) $request->input('country', 0);
        $state = (int) $request->input('state', 0);
        $city = (int) $request->input('city', 0);
        $deleted_by = (int) $request->input('deleted_by', 0);
        $deleted_by_role = $request->input('deleted_by_role', '');
        $leader = (int) $request->input('leader', 0);
        $fromDateFilter = $request->input('fromDate');
        $toDateFilter = $request->input('toDate');

        // Columns available for ordering
        $columns = ['branch_unique_id', 'name', 'mobile', 'email', 'created_at', 'updated_at'];

        // Ensure a valid column is selected for ordering
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        // Fetch branches with filtering
        $query = AdminBranch::query()->onlyTrashed();

        // Apply search filter
        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('branch_unique_id', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%")
                    ->orWhere('mobile', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        // Apply country filter
        if ($country > 0) {
            $query->where('country_id', $country);
        }

        // Apply state filter
        if ($state > 0) {
            $query->where('state_id', $state);
        }

        // Apply city filter
        if ($city > 0) {
            $query->where('city_id', $city);
        }

        // Apply updated by filter
        if ($deleted_by > 0 && $request->filled('deleted_by_role')) {
            $query->where('last_updated_by_id', $deleted_by)
                ->where('last_updated_by_type', $deleted_by_role === 'admins' ? 'App\Models\Admin' : 'App\Models\AdminEmployee');
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
        $totalRecords = AdminBranch::onlyTrashed()->count();
        $filteredRecords = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request) {

                // Helper function to format names
                $getFullName = fn($firstName, $lastName) => trim("{$firstName} {$lastName}") ?: 'N/A';

                $leader = $getFullName(optional($row->leader())->first_name, optional($row->leader())->last_name);
                $creator = $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name);
                $updator = $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name);

                // Checking permissions for actions
                $canRestore = $request->user->canPerform('Admin Branch', 'restore_trashed');
                $canDelete = $request->user->canPerform('Admin Branch', 'permanent_delete');

                // Generate URLs for edit and delete actions
                $restoreUrl = $canRestore ? route('admin.branches.restore', ['branchSlug' => $row->slug]) : null;
                $deleteUrl = $canDelete ? route('admin.branches.destroy', ['branchSlug' => $row->slug]) : null;

                $timezone = $request->user->country->timezones[0]['zoneName'] ?? 'UTC';

                return [
                    'branch_unique_id' => $row->branch_unique_id,
                    'name' => $row->name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'latitude' => $row->latitude,
                    'longitude' => $row->longitude,
                    'address' => $row->full_address,
                    'leader' => $leader,
                    'creator' => $creator,
                    'deletor' => $updator,
                    'created_at' => $row->created_at->setTimezone($timezone)->format('Y-m-d H:i:s'),
                    'deleted_at' => $row->deleted_at->setTimezone($timezone)->format('Y-m-d H:i:s'),
                    'actions' => [
                        'restore' => $restoreUrl,
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

    public function restore(string $branchSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Find the soft-deleted branch by slug
        $branch = AdminBranch::withTrashed()->where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'The requested branch could not be found.',
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
            if (!$request->user->canPerform('Admin Branch', 'restore_trashed')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Restore',
                        true, // Assuming the soft delete was successful
                        'The branch has been successfully restore.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to restore branch']),
                        $branch->toArray(), // Exclude latitude & longitude if necessary
                        AdminBranch::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Ensure the branch is actually deleted before attempting to restore
            if (!$branch->trashed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This branch is already active and does not need restoration.',
                ], 400);
            }

            // Restore the branch
            $branch->restore();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Restore',
                    true, // Assuming the soft delete was successful
                    'The branch has been successfully restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Restored completed']),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Branch restored successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Branch restoration failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Restore',
                    false, // Assuming the soft delete was successful
                    'The branch has been failed restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function destroy(string $branchSlug, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Find the soft-deleted branch by its slug
        $branch = AdminBranch::onlyTrashed()->where('slug', $branchSlug)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'The requested branch could not be found.',
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
            if (!$request->user->canPerform('Admin Branch', 'permanent_delete')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Branch',
                        'Force Delete',
                        true, // Assuming the soft delete was successful
                        'The branch has been successfully force delete.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to force deleted']),
                        $branch->toArray(), // Exclude latitude & longitude if necessary
                        AdminBranch::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Permanently delete the branch
            $branch->forceDelete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Force Delete',
                    true, // Assuming the soft delete was successful
                    'The branch has been successfully force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Force deleted completed']),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The branch has been permanently deleted and cannot be recovered.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Branch deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Branch',
                    'Force Delete',
                    false, // Assuming the soft delete was successful
                    'The branch has been failed force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $branch->toArray(), // Exclude latitude & longitude if necessary
                    AdminBranch::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function show($branchSlug, Request $request)
    {
        // Check user permissions
        if (!$request->user->canPerform('Admin Branch', 'view')) {
            abort(403, 'You do not have permission to view branche.');
        }

        // Fetch the branch by slug
        $branch = AdminBranch::where('slug', $branchSlug)->first();

        if (!$branch) {
            abort(404, 'The requested branch was not found.');
        }
        // prArr($branch->toArray(), 1);
        return view('admin.branch.show', [
            'branch' => $branch,
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $request->user->permissions,
            'userGroups' => ["admins" => Admin::orderBy('first_name', 'asc')->get()],
        ]);
    }
}
