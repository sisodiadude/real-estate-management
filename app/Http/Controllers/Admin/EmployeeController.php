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

            // Store binary fields separately
            $resume = $validatedData['resume'] ?? null;
            $profilePicture = $validatedData['profile_picture'] ?? null;
            $govtIds = $validatedData['govt_id'] ?? [];
            $educationCertificates = $validatedData['education_certificates'] ?? [];

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

            prArr($filePaths, 1);
            echo "All Done";
            die;
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
                'message' => 'An error occurred while creating the team.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
