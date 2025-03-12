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
use App\Models\AdminEmployee;
use App\Models\City;
use App\Models\State;
// Laravel Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

// HTTP Response Handling
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    /**
     * Handle File Uploads
     */
    private function uploadFile($file, $savePath, $multiple = false)
    {
        if (!$file) {
            return null;
        }

        // Define the full directory path
        $directory = public_path($savePath);

        // Ensure directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }

        // Handle single file upload
        if (!$multiple) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);
            return $savePath . '/' . $filename;
        }

        // Handle multiple file uploads
        $uploadedFiles = [];
        foreach ($file as $index => $singleFile) {
            $filename = time() . "_{$index}_" . uniqid() . '.' . $singleFile->getClientOriginalExtension();
            $singleFile->move($directory, $filename);
            $uploadedFiles[] = $savePath . '/' . $filename;
        }

        return $uploadedFiles;
    }

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
                        AdminEmployee::class
                    );
                }
                abort(403, 'You do not have permission to create employee.');
            }

            $validationRules = [
                // Basic Information
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
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
                'date_of_birth' => 'required|date|before:today|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'marital_status' => 'nullable|in:single,married,divorced,widowed',
                'nationality_id' => 'required|integer|exists:countries,id',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'account_status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',

                // Current Address
                'current_address_line1' => 'required|string|max:255',
                'current_address_line2' => 'nullable|string|max:500',
                'current_country_id' => 'required|integer|exists:countries,id',
                'current_state_id' => 'required|integer|exists:states,id',
                'current_city_id' => 'required|integer|exists:cities,id',
                'current_postal_code' => 'required|string|max:10',

                // Same as Current Address (Required Boolean)
                'same_as_current_address' => 'required|boolean',

                // Employment Details
                'designation' => 'required|string|max:100',
                'joining_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addMonths(2)->format('Y-m-d'),
                'probation_period' => 'nullable|integer|min:0|max:12',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'salary' => 'required|numeric|min:1',
                'allowances' => 'nullable|json',
                'deductions' => 'nullable|json',
                'salary_frequency' => 'required|in:monthly,weekly,biweekly,annually',
                'bank_account' => 'required|string|max:50',
                'bank_name' => 'required|string|max:100',
                'ifsc_swift_code' => 'required|string|alpha_num|max:20',
                'pan_tax_id' => 'required|string|alpha_num|max:20',

                // Emergency Contact Details
                'emergency_contact_name' => 'required|string|max:100',
                'emergency_contact_relation' => 'required|string|max:50',
                'emergency_contact_number' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],

                // File Uploads
                'resume' => 'required|file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // 5MB
                'profile_picture' => 'required|file|mimes:bmp,gif,heic,ico,jpeg,jpg,png,svg,tiff,webp|max:2048', // 2MB
                'govt_id' => 'required|array', // Ensure it's an array
                'govt_id.*' => 'file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // Multiple files, 5MB each
                'education_certificates' => 'required|array', // Ensure it's an array
                'education_certificates.*' => 'file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // Multiple files, 5MB each
            ];

            // Check if use_branch_smtp_credentials is true and add SMTP validation rules
            if ($request->input('use_branch_smtp_credentials') === true) {
                $validationRules = array_merge($validationRules, [
                    // Permanent Address
                    'permanent_address_line1' => 'required|string|max:255',
                    'permanent_address_line2' => 'nullable|string|max:500',
                    'permanent_country_id' => 'required|integer|exists:countries,id',
                    'permanent_state_id' => 'required|integer|exists:states,id',
                    'permanent_city_id' => 'required|integer|exists:cities,id',
                    'permanent_postal_code' => 'required|string|max:10',
                ]);
            } else {
                // If use_branch_smtp_credentials is false or not present, make these fields nullable
                $validationRules = array_merge($validationRules, [
                    // Permanent Address
                    'permanent_address_line1' => 'nullable',
                    'permanent_address_line2' => 'nullable',
                    'permanent_country_id' => 'nullable',
                    'permanent_state_id' => 'nullable',
                    'permanent_city_id' => 'nullable',
                    'permanent_postal_code' => 'nullable',
                ]);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // First Name
                'first_name.required' => 'First name is required.',
                'first_name.string' => 'First name must be a valid string.',
                'first_name.max' => 'First name cannot exceed 100 characters.',

                // Last Name
                'last_name.required' => 'Last name is required.',
                'last_name.string' => 'Last name must be a valid string.',
                'last_name.max' => 'Last name cannot exceed 100 characters.',

                // Email
                'email.required' => 'Team email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another employee.',

                // Alternative Email
                'alternative_email.required' => 'Employee alternative email is required.',
                'alternative_email.email' => 'Please enter a valid alternative email address.',
                'alternative_email.max' => 'Alternative email address cannot exceed 255 characters.',

                // Mobile
                'mobile.required' => 'Team mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another employee.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Alternate Mobile
                'alternate_mobile.required' => 'Employee alternate mobile is required.',
                'alternate_mobile.string' => 'Alternate mobile number must be a valid string.',
                'alternate_mobile.max' => 'Alternate mobile number cannot exceed 20 characters.',
                'alternate_mobile.regex' => 'Please enter a valid alternate mobile number (e.g., +1234567890, 9876543210).',

                // Date of Birth
                'date_of_birth.required' => 'Date of birth is required.',
                'date_of_birth.date' => 'Please enter a valid date of birth.',
                'date_of_birth.before' => 'Date of birth must be before today.',
                'date_of_birth.before_or_equal' => 'You must be at least 18 years old.',

                // Marital Status
                'marital_status.in' => 'Please select a valid marital status (Single, Married, Divorced, Widowed).',

                // Nationality
                'nationality_id.required' => 'Nationality is required.',
                'nationality_id.integer' => 'Nationality must be a valid integer ID.',
                'nationality_id.exists' => 'The selected nationality does not exist in the system.',

                // Blood Group
                'blood_group.in' => 'Please select a valid blood group (A+, A-, B+, B-, O+, O-, AB+, AB-).',

                // Status
                'account_status.required' => 'Status is required.',
                'account_status.in' => 'Please select a valid status (Active, Inactive, Suspended, Archived).',

                // Description
                'description.string' => 'Description must be a valid string.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Current Address
                'current_address_line1.required' => 'Current address line 1 is required.',
                'current_address_line1.string' => 'Current address line 1 must be a valid string.',
                'current_address_line1.max' => 'Current address line 1 cannot exceed 255 characters.',

                'current_address_line2.string' => 'Current address line 2 must be a valid string.',
                'current_address_line2.max' => 'Current address line 2 cannot exceed 500 characters.',

                // Current Address Location
                'current_country_id.required' => 'Current country is required.',
                'current_country_id.integer' => 'Current country must be a valid ID.',
                'current_country_id.exists' => 'The selected country does not exist in the system.',

                'current_state_id.required' => 'Current state is required.',
                'current_state_id.integer' => 'Current state must be a valid ID.',
                'current_state_id.exists' => 'The selected state does not exist in the system.',

                'current_city_id.required' => 'Current city is required.',
                'current_city_id.integer' => 'Current city must be a valid ID.',
                'current_city_id.exists' => 'The selected city does not exist in the system.',

                'current_postal_code.required' => 'Postal code is required.',
                'current_postal_code.string' => 'Postal code must be a valid string.',
                'current_postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Same as Current Address (Permanent Address Toggle)
                'same_as_current_address.required' => 'Selection for making permanent address same as current is required.',
                'same_as_current_address.boolean' => 'The value must be true or false to indicate if the permanent address is the same as the current address.',

                // Permanent Address
                'permanent_address_line1.required' => 'Permanent address line 1 is required.',
                'permanent_address_line1.string' => 'Permanent address line 1 must be a valid string.',
                'permanent_address_line1.max' => 'Permanent address line 1 cannot exceed 255 characters.',

                'permanent_address_line2.string' => 'Permanent address line 2 must be a valid string.',
                'permanent_address_line2.max' => 'Permanent address line 2 cannot exceed 500 characters.',

                // Permanent Address Location
                'permanent_country_id.required' => 'Permanent country is required.',
                'permanent_country_id.integer' => 'Permanent country must be a valid ID.',
                'permanent_country_id.exists' => 'The selected country does not exist in the system.',

                'permanent_state_id.required' => 'Permanent state is required.',
                'permanent_state_id.integer' => 'Permanent state must be a valid ID.',
                'permanent_state_id.exists' => 'The selected state does not exist in the system.',

                'permanent_city_id.required' => 'Permanent city is required.',
                'permanent_city_id.integer' => 'Permanent city must be a valid ID.',
                'permanent_city_id.exists' => 'The selected city does not exist in the system.',

                'postal_code.required' => 'Postal code is required.',
                'postal_code.string' => 'Postal code must be a valid string.',
                'postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Designation
                'designation.required' => 'Designation is required.',
                'designation.string' => 'Designation must be a valid string.',
                'designation.max' => 'Designation cannot exceed 100 characters.',

                // Joining Date
                'joining_date.required' => 'Joining date is required.',
                'joining_date.date' => 'Joining date must be a valid date.',
                'joining_date.after_or_equal' => 'Joining date cannot be in the past.',
                'joining_date.before_or_equal' => 'Joining date must be within the next 2 months.',

                // Probation Period
                'probation_period.integer' => 'Probation period must be a valid number.',
                'probation_period.min' => 'Probation period cannot be negative.',
                'probation_period.max' => 'Probation period cannot exceed 12 months.',

                // Employment Type
                'employment_type.required' => 'Employment type is required.',
                'employment_type.in' => 'Please select a valid employment type (Full-time, Part-time, Contract, Internship).',

                // Salary
                'salary.required' => 'Salary is required.',
                'salary.numeric' => 'Salary must be a valid number.',
                'salary.min' => 'Salary cannot be negative.',
                'salary_frequency.required' => 'Salary frequency is required.',
                'salary_frequency.in' => 'Salary frequency must be Monthly, Weekly, Biweekly, or Annually.',
                'allowances.json' => 'Allowances must be a valid JSON format.',
                'deductions.json' => 'Deductions hours must be a valid JSON format.',

                // Bank Account
                'bank_account.required' => 'Bank account number is required.',
                'bank_account.string' => 'Bank account number must be a valid string.',
                'bank_account.max' => 'Bank account number cannot exceed 50 characters.',

                // Bank Name
                'bank_name.required' => 'Bank name is required.',
                'bank_name.string' => 'Bank name must be a valid string.',
                'bank_name.max' => 'Bank name cannot exceed 100 characters.',

                'ifsc_swift_code.required' => 'IFSC/SWIFT code is required.',
                'ifsc_swift_code.alpha_num' => 'IFSC/SWIFT code must be alphanumeric.',
                'ifsc_swift_code.max' => 'IFSC/SWIFT code cannot exceed 20 characters.',

                'pan_tax_id.required' => 'PAN/TAX ID is required.',
                'pan_tax_id.alpha_num' => 'PAN/TAX ID must be alphanumeric.',
                'pan_tax_id.max' => 'PAN/TAX ID cannot exceed 20 characters.',

                'emergency_contact_name.required' => 'Emergency contact name is required.',
                'emergency_contact_name.string' => 'Emergency contact name must be a valid string.',
                'emergency_contact_name.max' => 'Emergency contact name cannot exceed 100 characters.',

                'emergency_contact_relation.required' => 'Emergency contact relation is required.',
                'emergency_contact_relation.string' => 'Emergency contact relation must be a valid string.',
                'emergency_contact_relation.max' => 'Emergency contact relation cannot exceed 50 characters.',

                'emergency_contact_number.required' => 'Emergency contact number is required.',
                'emergency_contact_number.regex' => 'Emergency contact number must be a valid phone number.',
                'emergency_contact_number.max' => 'Emergency contact number cannot exceed 20 characters.',

                'resume.required' => 'Resume is required.',
                'resume.mimes' => 'Resume format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'resume.max' => 'Resume file size must not exceed 5MB.',

                'profile_picture.required' => 'Profile picture is required.',
                'profile_picture.mimes' => 'Profile picture format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PNG, SVG, TIFF, or WEBP.',
                'profile_picture.max' => 'Profile picture size must not exceed 2MB.',

                'govt_id.required' => 'At least one government ID is required.',
                'govt_id.*.file' => 'Each government ID must be a valid file.',
                'govt_id.*.mimes' => 'Government ID format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'govt_id.*.max' => 'Each government ID file must not exceed 5MB.',

                'education_certificates.required' => 'At least one education certificate is required.',
                'education_certificates.*.file' => 'Each education certificate must be a valid file.',
                'education_certificates.*.mimes' => 'Education certificate format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'education_certificates.*.max' => 'Each education certificate file must not exceed 5MB.',
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
                        AdminEmployee::class
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

            // Remove binary fields from the data to be saved
            $employeeData = collect($validatedData)->except([
                'resume',
                'profile_picture',
                'govt_id',
                'education_certificates'
            ])->toArray();

            $employeeData['branch_id'] = $branch->id;
            $employeeData['department_id'] = $department->id;
            $employeeData['team_id'] = $team->id;

            if ($validatedData['same_as_current_address']) {
                $employeeData['permanent_address_line1'] = $validatedData['current_address_line1'];
                $employeeData['permanent_address_line2'] = $validatedData['current_address_line2'];
                $employeeData['permanent_country_id'] = $validatedData['current_country_id'];
                $employeeData['permanent_state_id'] = $validatedData['current_state_id'];
                $employeeData['permanent_city_id'] = $validatedData['current_city_id'];
                $employeeData['permanent_postal_code'] = $validatedData['current_postal_code'];
            } else {
                $employeeData['permanent_address_line1'] = $validatedData['permanent_address_line1'];
                $employeeData['permanent_address_line2'] = $validatedData['permanent_address_line2'];
                $employeeData['permanent_country_id'] = $validatedData['permanent_country_id'];
                $employeeData['permanent_state_id'] = $validatedData['permanent_state_id'];
                $employeeData['permanent_city_id'] = $validatedData['permanent_city_id'];
                $employeeData['permanent_postal_code'] = $validatedData['permanent_postal_code'];
            }

            // prArr($employeeData, 1);
            // Begin transaction
            DB::beginTransaction();

            // Create and store the team
            $employee = AdminEmployee::create($employeeData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Employee created successfully', ['team_id' => $team->id, 'name' => $team->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Create',
                    true,
                    'A new employee has been successfully created.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Employee creation activity recorded.']), // Description field updated
                    $employee->toArray(),
                    AdminEmployee::class
                );
            }

            $basePath = "assets/branches/$branch->branch_unique_id/departments/$department->department_unique_id/teams/$team->team_unique_id/employees/$employee->employee_unique_id";
            // Define paths dynamically
            $imageSaveInfo = [
                'resume' => [
                    'upload' => 'single',
                    'savePath' => "$basePath/resume"
                ],
                'profile_picture' => [
                    'upload' => 'single',
                    'savePath' => "$basePath/profile-pictures"
                ],
                'govt_id' => [
                    'upload' => 'multiple',
                    'savePath' => "$basePath/govt-ids"
                ],
                'education_certificates' => [
                    'upload' => 'multiple',
                    'savePath' => "$basePath/education-certificates"
                ]
            ];

            // Retrieve uploaded files
            $files = [
                'resume' => $request->file('resume'),
                'profile_picture' => $request->file('profile_picture'),
                'govt_id' => $request->file('govt_id'), // Multiple
                'education_certificates' => $request->file('education_certificates') // Multiple
            ];

            $filePaths = [];

            foreach ($files as $key => $file) {
                if ($file && isset($imageSaveInfo[$key])) {
                    $isMultiple = ($imageSaveInfo[$key]['upload'] === 'multiple');
                    $filePaths[$key] = $this->uploadFile($file, $imageSaveInfo[$key]['savePath'], $isMultiple);
                }
            }

            // Prepare data to update the employee with file paths
            $updateData = [
                'resume' => $filePaths['resume'] ?? null, // Single file path
                'profile_picture' => $filePaths['profile_picture'] ?? null, // Single file path
                'govt_id' => json_encode($filePaths['govt_id'] ?? []), // JSON-encoded array for multiple files
                'education_certificates' => json_encode($filePaths['education_certificates'] ?? []) // JSON-encoded array for multiple files
            ];

            // Update the employee record with file paths
            $employee->update($updateData);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Employee created successfully. Redirecting to the list.',
                'redirect_url' => route('admin.branches.departments.teams.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug]) // Replace 'team.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Employee creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

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
                    json_encode(['final_message' => $e->getMessage()]),
                    collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                    AdminEmployee::class
                );
            }

            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEmployees(string $branchSlug, string $departmentSlug, Request $request, string $teamSlug)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'error' => 'Invalid request.'], 400);
        }

        if (!$request->user->canPerform('Admin Employee', 'view_all')) {
            return response()->json(['status' => false, 'error' => 'You do not have permission to view Employees.'], 403);
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

        $columns = ['employee_unique_id', 'name', 'mobile', 'email', 'status', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        $query = AdminEmployee::byTeamID($team->id)->byDepartmentID($department->id)->byBranchID($branch->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($status);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('employee_unique_id', 'like', "%{$searchValue}%")
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

        if ($request->filled('fromDate')) {
            $toDate = $request->input('toDate', now()->toDateString());
            $query->whereBetween('date_of_joining', [$request->input('fromDate'), $toDate]);
        }

        $totalRecords = AdminEmployee::byTeamID($team->id)->byDepartmentID($department->id)->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch, $department, $team) {
                $getFullName = fn($first, $last) => trim("{$first} {$last}") ?: 'N/A';

                return [
                    'employee_unique_id' => $row->employee_unique_id,
                    'name' => $row->full_name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'status' => $row->account_status,
                    'creator' => $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name),
                    'updator' => $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name),
                    'created_at' => $row->created_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'updated_at' => $row->updated_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'actions' => [
                        'view' => $request->user->canPerform('Admin Employee', 'view') ? route('admin.branches.departments.teams.employees.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug, 'employeeUsername' => $row->username]) : null,
                        'edit' => $request->user->canPerform('Admin Employee', 'edit') ? route('admin.branches.departments.teams.employees.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug, 'employeeUsername' => $row->username]) : null,
                        'delete' => $request->user->canPerform('Admin Employee', 'soft_delete') ? route('admin.branches.departments.teams.employees.delete', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug, 'employeeUsername' => $row->username]) : null
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

    public function edit(string $branchSlug, string $departmentSlug, string $teamSlug, string $employeeUsername, Request $request)
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
            $employee = AdminEmployee::where('username', $employeeUsername)->first();

            // Determine which resource is missing
            $missing = [];
            if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
            if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
            if (!$team) $missing[] = "Team (Slug: $teamSlug)";
            if (!$employee) $missing[] = "Team (Slug: $employeeUsername)";

            // If any resource is missing, return a detailed 404 error
            if (!empty($missing)) {
                return abort(404, 'Resource Not Found: ' . implode(', ', $missing) . ' does not exist.');
            }

            // Fetch states if current_country_id  is not null
            $current_states = $employee->current_country_id  ? State::where('country_id', $employee->current_country_id)->orderBy('name', 'asc')->get() : collect([]);
            $permanent_states = $employee->permanent_country_id  ? State::where('country_id', $employee->permanent_country_id)->orderBy('name', 'asc')->get() : collect([]);

            // Fetch cities if state_id is not null
            $current_cities = $employee->current_state_id  ? City::where('state_id', $employee->current_state_id)->orderBy('name', 'asc')->get() : collect([]);
            $permanent_cities = $employee->permanent_state_id  ? City::where('state_id', $employee->permanent_state_id)->orderBy('name', 'asc')->get() : collect([]);

            return view('admin.employee.edit', [
                'branch' => $branch,
                'department' => $department,
                'team' => $team,
                'employee' => $employee,
                'user' => $request->user,
                'userType' => $request->userType,
                'hasPermissions' => $request->user->permissions,
                'countries' => Country::orderBy('name', 'asc')->get(),
                'current_states' => $current_states,
                'current_cities' => $current_cities,
                'permanent_states' => $permanent_states,
                'permanent_cities' => $permanent_cities,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error editing employee: ' . $e->getMessage());

            // Return a professional error response
            return abort(500, $e->getMessage() ?: 'An unexpected error occurred while loading the employee details. Please try again.');
        }
    }

    public function update(string $branchSlug, string $departmentSlug, string $teamSlug, string $employeeUsername, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        try {

            // Fetch the branch and department in a single query to optimize performance
            $branch = AdminBranch::where('slug', $branchSlug)->first();
            $department = AdminDepartment::where('slug', $departmentSlug)->first();
            $team = AdminTeam::where('slug', $teamSlug)->first();
            $employee = AdminEmployee::where('username', $employeeUsername)->first();

            // Determine which resource is missing
            $missing = [];
            if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
            if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
            if (!$team) $missing[] = "Team (Slug: $teamSlug)";
            if (!$employee) $missing[] = "Team (Slug: $employeeUsername)";

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
            if (!$request->user->canPerform('Admin Employee', 'create')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Update',
                        false,
                        'Failed to update a new employee.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to create employee']),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminEmployee::class
                    );
                }
                abort(403, 'You do not have permission to create employee.');
            }

            $validationRules = [
                // Basic Information
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:255|unique:admin_employees,email,' . $employee->id,
                'alternative_email' => 'required|email|max:255',
                'mobile' => [
                    'required',
                    'string',
                    'max:20,' . $employee->id . 'unique:admin_employees,mobile',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'alternate_mobile' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],
                'date_of_birth' => 'required|date|before:today|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'marital_status' => 'nullable|in:single,married,divorced,widowed',
                'nationality_id' => 'required|integer|exists:countries,id',
                'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
                'account_status' => 'required|in:active,inactive,suspended,archived',
                'description' => 'nullable|string|max:500',

                // Current Address
                'current_address_line1' => 'required|string|max:255',
                'current_address_line2' => 'nullable|string|max:500',
                'current_country_id' => 'required|integer|exists:countries,id',
                'current_state_id' => 'required|integer|exists:states,id',
                'current_city_id' => 'required|integer|exists:cities,id',
                'current_postal_code' => 'required|string|max:10',

                // Same as Current Address (Required Boolean)
                'same_as_current_address' => 'required|boolean',

                // Employment Details
                'designation' => 'required|string|max:100',
                'joining_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addMonths(2)->format('Y-m-d'),
                'probation_period' => 'nullable|integer|min:0|max:12',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'salary' => 'required|numeric|min:1',
                'allowances' => 'nullable|json',
                'deductions' => 'nullable|json',
                'salary_frequency' => 'required|in:monthly,weekly,biweekly,annually',
                'bank_account' => 'required|string|max:50',
                'bank_name' => 'required|string|max:100',
                'ifsc_swift_code' => 'required|string|alpha_num|max:20',
                'pan_tax_id' => 'required|string|alpha_num|max:20',

                // Emergency Contact Details
                'emergency_contact_name' => 'required|string|max:100',
                'emergency_contact_relation' => 'required|string|max:50',
                'emergency_contact_number' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9\s-]{10,20}$/'
                ],

                // File Uploads
                'resume' => 'nullable|file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // 5MB
                'profile_picture' => 'nullable|file|mimes:bmp,gif,heic,ico,jpeg,jpg,png,svg,tiff,webp|max:2048', // 2MB
                'govt_id' => 'nullable|array', // Ensure it's an array
                'govt_id.*' => 'file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // Multiple files, 5MB each
                'education_certificates' => 'nullable|array', // Ensure it's an array
                'education_certificates.*' => 'file|mimes:bmp,gif,heic,ico,jpeg,jpg,pdf,png,svg,tiff,webp|max:5120', // Multiple files, 5MB each
            ];

            // Check if use_branch_smtp_credentials is true and add SMTP validation rules
            if ($request->input('use_branch_smtp_credentials') === true) {
                $validationRules = array_merge($validationRules, [
                    // Permanent Address
                    'permanent_address_line1' => 'required|string|max:255',
                    'permanent_address_line2' => 'nullable|string|max:500',
                    'permanent_country_id' => 'required|integer|exists:countries,id',
                    'permanent_state_id' => 'required|integer|exists:states,id',
                    'permanent_city_id' => 'required|integer|exists:cities,id',
                    'permanent_postal_code' => 'required|string|max:10',
                ]);
            } else {
                // If use_branch_smtp_credentials is false or not present, make these fields nullable
                $validationRules = array_merge($validationRules, [
                    // Permanent Address
                    'permanent_address_line1' => 'nullable',
                    'permanent_address_line2' => 'nullable',
                    'permanent_country_id' => 'nullable',
                    'permanent_state_id' => 'nullable',
                    'permanent_city_id' => 'nullable',
                    'permanent_postal_code' => 'nullable',
                ]);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $validationRules, [
                // First Name
                'first_name.required' => 'First name is required.',
                'first_name.string' => 'First name must be a valid string.',
                'first_name.max' => 'First name cannot exceed 100 characters.',

                // Last Name
                'last_name.required' => 'Last name is required.',
                'last_name.string' => 'Last name must be a valid string.',
                'last_name.max' => 'Last name cannot exceed 100 characters.',

                // Email
                'email.required' => 'Employee email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email address cannot exceed 255 characters.',
                'email.unique' => 'This email is already associated with another employee.',

                // Alternative Email
                'alternative_email.required' => 'Employee alternative email is required.',
                'alternative_email.email' => 'Please enter a valid alternative email address.',
                'alternative_email.max' => 'Alternative email address cannot exceed 255 characters.',

                // Mobile
                'mobile.required' => 'Employee mobile is required.',
                'mobile.string' => 'Mobile number must be a valid string.',
                'mobile.max' => 'Mobile number cannot exceed 20 characters.',
                'mobile.unique' => 'This mobile number is already associated with another employee.',
                'mobile.regex' => 'Please enter a valid mobile number (e.g., +1234567890, 9876543210).',

                // Alternate Mobile
                'alternate_mobile.required' => 'Employee alternate mobile is required.',
                'alternate_mobile.string' => 'Alternate mobile number must be a valid string.',
                'alternate_mobile.max' => 'Alternate mobile number cannot exceed 20 characters.',
                'alternate_mobile.regex' => 'Please enter a valid alternate mobile number (e.g., +1234567890, 9876543210).',

                // Date of Birth
                'date_of_birth.required' => 'Date of birth is required.',
                'date_of_birth.date' => 'Please enter a valid date of birth.',
                'date_of_birth.before' => 'Date of birth must be before today.',
                'date_of_birth.before_or_equal' => 'You must be at least 18 years old.',

                // Marital Status
                'marital_status.in' => 'Please select a valid marital status (Single, Married, Divorced, Widowed).',

                // Nationality
                'nationality_id.required' => 'Nationality is required.',
                'nationality_id.integer' => 'Nationality must be a valid integer ID.',
                'nationality_id.exists' => 'The selected nationality does not exist in the system.',

                // Blood Group
                'blood_group.in' => 'Please select a valid blood group (A+, A-, B+, B-, O+, O-, AB+, AB-).',

                // Status
                'account_status.required' => 'Status is required.',
                'account_status.in' => 'Please select a valid status (Active, Inactive, Suspended, Archived).',

                // Description
                'description.string' => 'Description must be a valid string.',
                'description.max' => 'Description cannot exceed 500 characters.',

                // Current Address
                'current_address_line1.required' => 'Current address line 1 is required.',
                'current_address_line1.string' => 'Current address line 1 must be a valid string.',
                'current_address_line1.max' => 'Current address line 1 cannot exceed 255 characters.',

                'current_address_line2.string' => 'Current address line 2 must be a valid string.',
                'current_address_line2.max' => 'Current address line 2 cannot exceed 500 characters.',

                // Current Address Location
                'current_country_id.required' => 'Current country is required.',
                'current_country_id.integer' => 'Current country must be a valid ID.',
                'current_country_id.exists' => 'The selected country does not exist in the system.',

                'current_state_id.required' => 'Current state is required.',
                'current_state_id.integer' => 'Current state must be a valid ID.',
                'current_state_id.exists' => 'The selected state does not exist in the system.',

                'current_city_id.required' => 'Current city is required.',
                'current_city_id.integer' => 'Current city must be a valid ID.',
                'current_city_id.exists' => 'The selected city does not exist in the system.',

                'current_postal_code.required' => 'Postal code is required.',
                'current_postal_code.string' => 'Postal code must be a valid string.',
                'current_postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Same as Current Address (Permanent Address Toggle)
                'same_as_current_address.required' => 'Selection for making permanent address same as current is required.',
                'same_as_current_address.boolean' => 'The value must be true or false to indicate if the permanent address is the same as the current address.',

                // Permanent Address
                'permanent_address_line1.required' => 'Permanent address line 1 is required.',
                'permanent_address_line1.string' => 'Permanent address line 1 must be a valid string.',
                'permanent_address_line1.max' => 'Permanent address line 1 cannot exceed 255 characters.',

                'permanent_address_line2.string' => 'Permanent address line 2 must be a valid string.',
                'permanent_address_line2.max' => 'Permanent address line 2 cannot exceed 500 characters.',

                // Permanent Address Location
                'permanent_country_id.required' => 'Permanent country is required.',
                'permanent_country_id.integer' => 'Permanent country must be a valid ID.',
                'permanent_country_id.exists' => 'The selected country does not exist in the system.',

                'permanent_state_id.required' => 'Permanent state is required.',
                'permanent_state_id.integer' => 'Permanent state must be a valid ID.',
                'permanent_state_id.exists' => 'The selected state does not exist in the system.',

                'permanent_city_id.required' => 'Permanent city is required.',
                'permanent_city_id.integer' => 'Permanent city must be a valid ID.',
                'permanent_city_id.exists' => 'The selected city does not exist in the system.',

                'postal_code.required' => 'Postal code is required.',
                'postal_code.string' => 'Postal code must be a valid string.',
                'postal_code.max' => 'Postal code cannot exceed 10 characters.',

                // Designation
                'designation.required' => 'Designation is required.',
                'designation.string' => 'Designation must be a valid string.',
                'designation.max' => 'Designation cannot exceed 100 characters.',

                // Joining Date
                'joining_date.required' => 'Joining date is required.',
                'joining_date.date' => 'Joining date must be a valid date.',
                'joining_date.after_or_equal' => 'Joining date cannot be in the past.',
                'joining_date.before_or_equal' => 'Joining date must be within the next 2 months.',

                // Probation Period
                'probation_period.integer' => 'Probation period must be a valid number.',
                'probation_period.min' => 'Probation period cannot be negative.',
                'probation_period.max' => 'Probation period cannot exceed 12 months.',

                // Employment Type
                'employment_type.required' => 'Employment type is required.',
                'employment_type.in' => 'Please select a valid employment type (Full-time, Part-time, Contract, Internship).',

                // Salary
                'salary.required' => 'Salary is required.',
                'salary.numeric' => 'Salary must be a valid number.',
                'salary.min' => 'Salary cannot be negative.',
                'salary_frequency.required' => 'Salary frequency is required.',
                'salary_frequency.in' => 'Salary frequency must be Monthly, Weekly, Biweekly, or Annually.',
                'allowances.json' => 'Allowances must be a valid JSON format.',
                'deductions.json' => 'Deductions hours must be a valid JSON format.',

                // Bank Account
                'bank_account.required' => 'Bank account number is required.',
                'bank_account.string' => 'Bank account number must be a valid string.',
                'bank_account.max' => 'Bank account number cannot exceed 50 characters.',

                // Bank Name
                'bank_name.required' => 'Bank name is required.',
                'bank_name.string' => 'Bank name must be a valid string.',
                'bank_name.max' => 'Bank name cannot exceed 100 characters.',

                'ifsc_swift_code.required' => 'IFSC/SWIFT code is required.',
                'ifsc_swift_code.alpha_num' => 'IFSC/SWIFT code must be alphanumeric.',
                'ifsc_swift_code.max' => 'IFSC/SWIFT code cannot exceed 20 characters.',

                'pan_tax_id.required' => 'PAN/TAX ID is required.',
                'pan_tax_id.alpha_num' => 'PAN/TAX ID must be alphanumeric.',
                'pan_tax_id.max' => 'PAN/TAX ID cannot exceed 20 characters.',

                'emergency_contact_name.required' => 'Emergency contact name is required.',
                'emergency_contact_name.string' => 'Emergency contact name must be a valid string.',
                'emergency_contact_name.max' => 'Emergency contact name cannot exceed 100 characters.',

                'emergency_contact_relation.required' => 'Emergency contact relation is required.',
                'emergency_contact_relation.string' => 'Emergency contact relation must be a valid string.',
                'emergency_contact_relation.max' => 'Emergency contact relation cannot exceed 50 characters.',

                'emergency_contact_number.required' => 'Emergency contact number is required.',
                'emergency_contact_number.regex' => 'Emergency contact number must be a valid phone number.',
                'emergency_contact_number.max' => 'Emergency contact number cannot exceed 20 characters.',

                'resume.required' => 'Resume is required.',
                'resume.mimes' => 'Resume format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'resume.max' => 'Resume file size must not exceed 5MB.',

                'profile_picture.required' => 'Profile picture is required.',
                'profile_picture.mimes' => 'Profile picture format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PNG, SVG, TIFF, or WEBP.',
                'profile_picture.max' => 'Profile picture size must not exceed 2MB.',

                'govt_id.required' => 'At least one government ID is required.',
                'govt_id.*.file' => 'Each government ID must be a valid file.',
                'govt_id.*.mimes' => 'Government ID format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'govt_id.*.max' => 'Each government ID file must not exceed 5MB.',

                'education_certificates.required' => 'At least one education certificate is required.',
                'education_certificates.*.file' => 'Each education certificate must be a valid file.',
                'education_certificates.*.mimes' => 'Education certificate format must be BMP, GIF, HEIC, ICO, JPEG, JPG, PDF, PNG, SVG, TIFF, or WEBP.',
                'education_certificates.*.max' => 'Each education certificate file must not exceed 5MB.',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Update',
                        false,
                        'Failed to update a new employee.',
                        $request->latitude,
                        $request->longitude,
                        json_encode($validator->errors()->toArray()),
                        collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                        AdminEmployee::class
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

            // Remove binary fields from the data to be saved
            $employeeData = collect($validatedData)->except([
                'resume',
                'profile_picture',
                'govt_id',
                'education_certificates'
            ])->toArray();

            $employeeData['branch_id'] = $branch->id;
            $employeeData['department_id'] = $department->id;
            $employeeData['team_id'] = $team->id;

            if ($validatedData['same_as_current_address']) {
                $employeeData['permanent_address_line1'] = $validatedData['current_address_line1'];
                $employeeData['permanent_address_line2'] = $validatedData['current_address_line2'];
                $employeeData['permanent_country_id'] = $validatedData['current_country_id'];
                $employeeData['permanent_state_id'] = $validatedData['current_state_id'];
                $employeeData['permanent_city_id'] = $validatedData['current_city_id'];
                $employeeData['permanent_postal_code'] = $validatedData['current_postal_code'];
            } else {
                $employeeData['permanent_address_line1'] = $validatedData['permanent_address_line1'];
                $employeeData['permanent_address_line2'] = $validatedData['permanent_address_line2'];
                $employeeData['permanent_country_id'] = $validatedData['permanent_country_id'];
                $employeeData['permanent_state_id'] = $validatedData['permanent_state_id'];
                $employeeData['permanent_city_id'] = $validatedData['permanent_city_id'];
                $employeeData['permanent_postal_code'] = $validatedData['permanent_postal_code'];
            }

            // prArr($employeeData, 1);
            // Begin transaction
            DB::beginTransaction();

            // Store old values before updating
            $oldValues = $employee->getOriginal();

            // Update branch data
            $employee->update($employeeData);

            // Commit transaction
            DB::commit();

            // Log success
            Log::info('Employee updated successfully', ['team_id' => $team->id, 'name' => $team->name]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Create',
                    true,
                    'A new employee has been successfully updated.',
                    $request->latitude,
                    $request->longitude,
                    json_encode(['final_message' => 'Employee updation activity recorded.']), // Description field updated
                    $employee->toArray(),
                    AdminEmployee::class
                );
            }

            $basePath = "assets/branches/$branch->branch_unique_id/departments/$department->department_unique_id/teams/$team->team_unique_id/employees/$employee->employee_unique_id";
            // Define paths dynamically
            $imageSaveInfo = [
                'resume' => [
                    'upload' => 'single',
                    'savePath' => "$basePath/resume"
                ],
                'profile_picture' => [
                    'upload' => 'single',
                    'savePath' => "$basePath/profile-pictures"
                ],
                'govt_id' => [
                    'upload' => 'multiple',
                    'savePath' => "$basePath/govt-ids"
                ],
                'education_certificates' => [
                    'upload' => 'multiple',
                    'savePath' => "$basePath/education-certificates"
                ]
            ];

            // Retrieve uploaded files
            $files = [
                'resume' => $request->file('resume'),
                'profile_picture' => $request->file('profile_picture'),
                'govt_id' => $request->file('govt_id'), // Multiple
                'education_certificates' => $request->file('education_certificates') // Multiple
            ];

            $filePaths = [];

            foreach ($files as $key => $file) {
                if ($file && isset($imageSaveInfo[$key])) {
                    $isMultiple = ($imageSaveInfo[$key]['upload'] === 'multiple');
                    $filePaths[$key] = $this->uploadFile($file, $imageSaveInfo[$key]['savePath'], $isMultiple);
                }
            }

            // Prepare data to update the employee with file paths
            $updateData = [
                'resume' => $filePaths['resume'] ?? null, // Single file path
                'profile_picture' => $filePaths['profile_picture'] ?? null, // Single file path
                'govt_id' => json_encode($filePaths['govt_id'] ?? []), // JSON-encoded array for multiple files
                'education_certificates' => json_encode($filePaths['education_certificates'] ?? []) // JSON-encoded array for multiple files
            ];

            // Update the employee record with file paths
            $employee->update($updateData);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Employee updated successfully. Redirecting to the list.',
                'redirect_url' => route('admin.branches.departments.teams.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug]) // Replace 'team.list' with your actual route name
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            // Log the error
            Log::error('Employee updation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Update',
                    false,
                    'Failed to update a new employee.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => $e->getMessage()]),
                    collect($request->except(['latitude', 'longitude', '_token', 'user', 'userType']))->toArray(), // Exclude latitude & longitude
                    AdminEmployee::class
                );
            }

            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(string $branchSlug, string $departmentSlug, string $teamSlug, string $employeeUsername, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::where('slug', $teamSlug)->first();
        $employee = AdminEmployee::where('username', $employeeUsername)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Team (Slug: $teamSlug)";
        if (!$employee) $missing[] = "Team (Slug: $employeeUsername)";

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
            if (!$request->user->canPerform('Admin Employee', 'soft_delete')) {
                if ($activityLogModel) {
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Soft Delete',
                        true, // Assuming the soft delete was successful
                        'The employee has been successfully soft deleted.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to soft delete employee']),
                        $employee->toArray(), // Exclude latitude & longitude if necessary
                        AdminEmployee::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Soft delete the branch
            $employee->delete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Soft Delete',
                    true, // Assuming the soft delete was successful
                    'The employee has been successfully soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Soft deletion completed']),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The employee has been successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Employee deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Soft Delete',
                    false, // Assuming the soft delete was successful
                    'The employee has been failed soft deleted.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTrashedEmployees(string $branchSlug, string $departmentSlug, Request $request, string $teamSlug)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => false, 'error' => 'Invalid request.'], 400);
        }

        if (!$request->user->canPerform('Admin Employee', 'view_all')) {
            return response()->json(['status' => false, 'error' => 'You do not have permission to view Employees.'], 403);
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

        $columns = ['employee_unique_id', 'name', 'mobile', 'email', 'status', 'created_at', 'updated_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $orderDirection = in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'desc';

        $query = AdminEmployee::onlyTrashed()->byTeamID($team->id)->byDepartmentID($department->id)->byBranchID($branch->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($status);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('employee_unique_id', 'like', "%{$searchValue}%")
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

        if ($request->filled('fromDate')) {
            $toDate = $request->input('toDate', now()->toDateString());
            $query->whereBetween('date_of_joining', [$request->input('fromDate'), $toDate]);
        }

        $totalRecords = AdminEmployee::onlyTrashed()->byTeamID($team->id)->byDepartmentID($department->id)->byBranchID($branch->id)->count();
        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($row) use ($request, $branch, $department, $team) {
                $getFullName = fn($first, $last) => trim("{$first} {$last}") ?: 'N/A';

                return [
                    'employee_unique_id' => $row->employee_unique_id,
                    'name' => $row->full_name,
                    'mobile' => $row->mobile,
                    'email' => $row->email,
                    'status' => $row->account_status,
                    'creator' => $getFullName(optional($row->creator_details)->first_name, optional($row->creator_details)->last_name),
                    'deletor' => $getFullName(optional($row->updator_details)->first_name, optional($row->updator_details)->last_name),
                    'created_at' => $row->created_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'deleted_at' => $row->updated_at->setTimezone($request->user->country->timezones[0]['zoneName'] ?? 'UTC')->format('Y-m-d H:i:s'),
                    'actions' => [
                        'restore' => $request->user->canPerform('Admin Employee', 'restore_trashed') ? route('admin.branches.departments.teams.employees.restore', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug, 'employeeUsername' => $row->username]) : null,
                        'delete' => $request->user->canPerform('Admin Employee', 'permanent_delete') ? route('admin.branches.departments.teams.employees.destroy', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug, 'employeeUsername' => $row->username]) : null
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

    public function restore(string $branchSlug, string $departmentSlug, string $teamSlug, string $employeeUsername, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::where('slug', $teamSlug)->first();
        $employee = AdminEmployee::onlyTrashed()->where('username', $employeeUsername)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Team (Slug: $teamSlug)";
        if (!$employee) $missing[] = "Employee (Slug: $employeeUsername)";

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
            if (!$request->user->canPerform('Admin Employee', 'restore_trashed')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Restore',
                        true, // Assuming the soft delete was successful
                        'The employee has been successfully restore.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to restore employee']),
                        $employee->toArray(), // Exclude latitude & longitude if necessary
                        AdminEmployee::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Ensure the employee is actually deleted before attempting to restore
            if (!$employee->trashed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This employee is already active and does not need restoration.',
                ], 400);
            }

            // Restore the employee
            $employee->restore();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Restore',
                    true, // Assuming the soft delete was successful
                    'The employee has been successfully restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Restored completed']),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Employee restored successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Employee restoration failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Restore',
                    false, // Assuming the soft delete was successful
                    'The employee has been failed restore.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }

    public function destroy(string $branchSlug, string $departmentSlug, string $teamSlug, string $employeeUsername, Request $request)
    {
        $activityLogModel = strtolower($request->userType) === 'admin' ? AdminActivityLog::class : null;

        // Fetch the branch and department in a single query to optimize performance
        $branch = AdminBranch::where('slug', $branchSlug)->first();
        $department = AdminDepartment::where('slug', $departmentSlug)->first();
        $team = AdminTeam::where('slug', $teamSlug)->first();
        $employee = AdminEmployee::onlyTrashed()->where('username', $employeeUsername)->first();

        // Determine which resource is missing
        $missing = [];
        if (!$branch) $missing[] = "Branch (Slug: $branchSlug)";
        if (!$department) $missing[] = "Department (Slug: $departmentSlug)";
        if (!$team) $missing[] = "Employee (Slug: $teamSlug)";
        if (!$employee) $missing[] = "Employee (Username: $employeeUsername)";

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
            if (!$request->user->canPerform('Admin Employee', 'permanent_delete')) {
                if ($activityLogModel) {
                    // Store Activity Log
                    $activityLogModel::storeLog(
                        $request->user,
                        'Admin Employee',
                        'Force Delete',
                        true, // Assuming the soft delete was successful
                        'The employee has been failed force delete.',
                        $request->latitude ?? null,
                        $request->longitude ?? null,
                        json_encode(['final_message' => 'not authorized to force deleted']),
                        $employee->toArray(), // Exclude latitude & longitude if necessary
                        AdminTeam::class
                    );
                }
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Permanently delete the employee
            $employee->forceDelete();

            if ($activityLogModel) {
                // Store Activity Log
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Force Delete',
                    true, // Assuming the soft delete was successful
                    'The employee has been successfully force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode(['final_message' => 'Force deleted completed']),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'The employee has been permanently deleted and cannot be recovered.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Employee deletion failed', ['error' => $e->getMessage()]);

            // Store Activity Log
            if ($activityLogModel) {
                $activityLogModel::storeLog(
                    $request->user,
                    'Admin Employee',
                    'Force Delete',
                    false, // Assuming the soft delete was successful
                    'The employee has been failed force delete.',
                    $request->latitude ?? null,
                    $request->longitude ?? null,
                    json_encode($e->getMessage()),
                    $employee->toArray(), // Exclude latitude & longitude if necessary
                    AdminEmployee::class
                );
            }

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred while processing your request. Please try again later.',
                'error_details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
            ], 500);
        }
    }
}
