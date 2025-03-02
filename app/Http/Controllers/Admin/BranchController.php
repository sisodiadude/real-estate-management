<?php

namespace App\Http\Controllers\Admin;

// Base Controller
use App\Http\Controllers\Controller;

// Models
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
        try {

            // Check user permissions
            if (!$request->user->canPerform('Admin Branch', 'Create')) {
                abort(403, 'You do not have permission to create branches.');
            }

            $validationRules = [
                // Basic Information
                'name' => 'required|string|max:100|unique:admin_branches,name',
                'slug' => 'nullable|string|max:255|unique:admin_branches,slug',
                'email' => 'nullable|email|max:255|unique:admin_branches,email',
                'mobile' => 'nullable|string|max:20|unique:admin_branches,mobile|regex:/^\+?[0-9\s-]{10,20}$/',
                'date_of_start' => 'nullable|date',
                'status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|max:2048', // Max 2MB, accepts image files only

                // Address & Location
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'postal_code' => 'required|string|max:10',

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

                // GSTIN
                'gstin.string' => 'GSTIN must be a valid string.',
                'gstin.unique' => 'This GSTIN is already in use.',

                // Branch Type
                'branch_type.required' => 'Branch type is required.',
                'branch_type.in' => 'Invalid branch type selected.',

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
                'branch_status.required' => 'Branch status is required.',
                'branch_status.in' => 'Invalid branch status selected.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            // Extract validated data
            $validatedData = $validator->validated();

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
        ]);
    }
}
