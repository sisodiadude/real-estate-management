<?php

namespace App\Http\Controllers\Admin;

// Models
use App\Models\Admin;
use App\Models\EmailConfig;
use App\Models\AdminLoginLog;

// Illuminates
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

// Providers
use App\Providers\RouteServiceProvider;

// Helpers
use App\Helpers\EmailHelper;

// Controllers
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        // Check if the user is already authenticated
        if (Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                abort(response()->json(['status' => true, 'redirect_url' => route('admin.dashboard')], 200));
            }

            return redirect()->route('admin.dashboard')->send();
        }
    }

    /**
     * Show the admin login page.
     */
    public function index()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    private function handleTwoFactorAuth($user, $userType, $latitude, $longitude, $loginMethod)
    {

        // Choose the appropriate login log model based on user type
        // $loginLogModel = $userType === 'admin_employee' ? AdminEmployeeLoginLog::class : AdminLoginLog::class;

        $loginLogModel = $userType === 'admin' ? AdminLoginLog::class : null;

        // Generate OTP using the dedicated method
        $user->generateTwoFactorLoginCode();

        // Initialize response details
        $responses = [];
        $errors = [];

        // Log OTP event
        $logMessageParts = ["Two-Factor Authentication (2FA) OTP initiated for {$user->username}."];

        // Check verified methods
        $verifiedDetail = array_keys($user->getVerifiedDetails());

        // Send OTP via email if email is verified
        if (in_array('email', $verifiedDetail)) {
            $emailSettings = EmailConfig::byModuleAndAction('admin-auth', 'two_factor')->first();

            if ($emailSettings) {
                try {
                    // Replace placeholders in email template
                    $templateHtml = str_replace('{{login_otp}}', $user->login_otp, $emailSettings->template);
                    $templateHtml = str_replace('{{login_otp_expires_at}}', $user->login_otp_expires_at->diffForHumans(), $templateHtml);
                    $emailSettings->template = $templateHtml;

                    // Recipient info
                    $recipients = [
                        (object) [
                            'email' => $user->email,
                            'name' => $user->name,
                        ]
                    ];

                    // Send email using helper
                    $response = EmailHelper::sendEmail($emailSettings, $emailSettings->template, $recipients);

                    if ($response->getData()->success) {
                        $responses[] = "Two-Factor Authentication (2FA) OTP sent via email.";
                        $logMessageParts[] = "OTP successfully sent to registered email: {$user->email}.";
                    } else {
                        $errors[] = "Failed to send OTP email: " . $response->getData()->message;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error sending OTP email: " . $e->getMessage();
                }
            } else {
                $errors[] = "Email settings not configured properly.";
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'status' => false,
                'message' => empty($errors) ? 'Unknown error occurred' : implode(' | ', $errors),
            ], 500);
        }

        // Send OTP via mobile if mobile is verified
        if (in_array('mobile', $verifiedDetail)) {
            $responses[] = "Two-Factor Authentication (2FA) OTP sent via mobile.";
            $logMessageParts[] = "OTP successfully sent to registered mobile.";
        }

        // Finalize log message and error handling
        $logData = [];
        if (!empty($logMessageParts)) {
            $logData['message'] = implode(' | ', $logMessageParts);
        }
        if (!empty($errors)) {
            $logData['error'] = implode(' | ', $errors);
        }

        $loginLogModel::logAttempt(
            $user,
            'Authentication',
            '2FA OTP Sent',
            $user->username,
            true,
            json_encode($logData),
            $latitude,
            $longitude,
            $loginMethod
        );

        // Construct final response
        if (empty($errors)) {
            return response()->json([
                'status' => true,
                'message' => implode(' and ', $responses),
                'login_otp_expires_at' => $user->login_otp_expires_at, // Send expiry time to frontend
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => empty($errors) ? 'Unknown error occurred' : implode(' | ', $errors),
            ], 500);
        }
    }

    /**
     * Login admin
     *
     * @param Request $request
     */
    public function login(Request $request)
    {
        // Validate the login request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'username.required' => 'Please enter your email, phone number, or username.',
            'password.required' => 'Please enter your password.',
            'password.min'      => 'Your password must be at least 8 characters long.',
            'latitude.required' => 'Location data is required to verify login activity.',
            'latitude.numeric'  => 'Invalid location data. Latitude must be a number.',
            'latitude.between'  => 'Latitude must be within the valid range (-90 to 90).',
            'longitude.required' => 'Location data is required to verify login activity.',
            'longitude.numeric' => 'Invalid location data. Longitude must be a number.',
            'longitude.between' => 'Longitude must be within the valid range (-180 to 180).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'There were errors in your login request.',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        // Determine login type
        $isEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL) !== false;
        $isMobile = preg_match('/^\+?[1-9]\d{1,14}$/', $request->username) === 1;
        $isUsername = !$isEmail && !$isMobile;
        $loginMethod = $isEmail ? 'email' : ($isMobile ? 'mobile' : 'username');

        // Attempt to find the user in both Admin and AdminEmployee
        $admin = Admin::findByUsername($request->username)->first();
        // $employee = AdminEmployee::findByUsername($request->username)->first();

        $user = $admin ?? $employee ?? null;
        // $userType = $admin ? 'admin' : ($employee ? 'admin_employee' : null);
        $userType = $admin ? 'admin' : null;

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Login failed. The username or email provided does not match any account in our system. Please check your credentials and try again.',
            ], 404);
        }

        // Choose the appropriate login log model based on user type
        // $loginLogModel = $userType === 'admin_employee' ? AdminEmployeeLoginLog::class : AdminLoginLog::class;
        $loginLogModel = $userType === 'admin' ? AdminLoginLog::class : null;

        // Verify password
        if (!Hash::check($request->password, $user->password)) {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => 'Login failed: Incorrect password entered.']),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => 'Login failed. The password you entered is incorrect. Please try again or reset your password if you have forgotten it.',
            ], 401);
        }

        // Check if the account is inactive or suspended
        if (strtolower($user->account_status) !== 'active') {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => "Login failed: Account is currently {$user->status}. Access denied."]),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => "Your account is currently {$user->status}. Please contact support for assistance.",
            ], 403);
        }

        // Check employment status for employees
        if ($userType == 'admin_employee' && $user->employment_status !== 'active') {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => "Login failed: Employee is marked as {$user->employment_status}."]),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => "Your employment status is currently '{$user->employment_status}', preventing login. Please contact HR or your administrator.",
            ], 403);
        }

        // Check if the account start date is in the future
        if ($user->start_date > now()) {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => "Login failed: Employee attempted to log in before the activation date ({$user->start_date})."]),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => "Your account is not yet active. You can log in starting from {$user->start_date->format('Y-m-d')}. Please try again later.",
            ], 403);
        }

        // Check verification status
        $verifiedDetail = array_keys($user->getVerifiedDetails());

        if (!in_array('email', $verifiedDetail) && $loginMethod == 'email') {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => "Login failed: Account has an unverified email address."]),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => 'Login failed. Your email address has not been verified. Please check your email and complete the verification process.',
            ], 403);
        }

        if (!in_array('mobile', $verifiedDetail) && $loginMethod == 'mobile') {

            $loginLogModel::logAttempt(
                $user,
                'Authentication',
                'Login Attempt',
                $request->username,
                false,
                json_encode(['error' => "Login failed: Account has an unverified mobile number."]),
                $request->latitude,
                $request->longitude,
                $loginMethod
            );

            return response()->json([
                'status' => false,
                'message' => 'Login failed. Your mobile number has not been verified. Please verify your phone number before logging in.',
            ], 403);
        }

        // Handle two-factor authentication (if enabled)
        if ($user->is_two_factor_enabled) {
            return $this->handleTwoFactorAuth($user, $userType, $request->latitude, $request->longitude, $loginMethod);
        }

        // Handle "Remember Me" functionality
        if ($request->has('remember_my_choice') && $request->remember_my_choice == 'on') {
            $credentials = json_encode(['username' => $request->username, 'password' => $request->password]);
            Cookie::queue('admin_credentials', $credentials, 525600);  // 1 year in minutes
        } else {
            Cookie::queue(Cookie::forget('admin_credentials'));
        }

        // Successful login
        $loginLogModel::logAttempt(
            $user,
            'Authentication',
            'Password Login Successful',
            $request->username,
            true,
            json_encode(['message' => "Login successful. User authenticated using {$loginMethod}."]),
            $request->latitude,
            $request->longitude,
            $loginMethod
        );

        Auth::guard($userType)->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successful! Redirecting to the admin dashboard...',
            'redirect_url' => route("admin.dashboard"),
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'login_otp' => 'required|digits:6',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'remember_my_choice' => 'nullable|boolean',
        ], [
            'username.required' => 'Please enter your email, phone number, or username.',
            'password.required' => 'Please enter your password.',
            'password.min'      => 'Your password must be at least 8 characters long.',
            'login_otp.required' => 'Please enter the OTP sent to your email or mobile.',
            'login_otp.digits' => 'The OTP must be exactly 6 digits.',
            'latitude.required' => 'Location data is required for security verification.',
            'longitude.required' => 'Location data is required for security verification.',
            'remember_my_choice.boolean' => 'The remember my choice value must be a boolean.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed. Please check the provided details and try again.',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        // Determine login type
        $isEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL) !== false;
        $isMobile = preg_match('/^\+?[1-9]\d{1,14}$/', $request->username) === 1;
        $loginMethod = $isEmail ? 'email' : ($isMobile ? 'mobile' : 'username');

        // Check if user exists
        $admin = Admin::findByUsername($request->username)->first();
        // $employee = AdminEmployee::findByUsername($request->username)->first();
        $user = $admin ?? $employee ?? null;
        // $userType = $admin ? 'admin' : ($employee ? 'admin_employee' : null);
        $userType = $admin ? 'admin' : null;

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No account found with the provided credentials. Please check and try again.',
                'errors' => ['username' => ['User not found.']]
            ], 404);
        }

        // Choose the appropriate login log model based on user type
        // $loginLogModel = $userType === 'admin_employee' ? AdminEmployeeLoginLog::class : AdminLoginLog::class;
        $loginLogModel = $userType === 'admin' ? AdminLoginLog::class : null;

        // Check if 2FA is enabled
        if (!$user->is_two_factor_enabled) {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Verification Failed', $request->username, false, json_encode([
                'error' => 'Two-Factor Authentication (2FA) is not enabled for this account.'
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => 'Two-Factor Authentication (2FA) is not enabled for your account. Please contact support.',
            ], 403);
        }

        // Check account status
        if (strtolower($user->account_status) !== 'active') {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Verification Failed', $request->username, false, json_encode([
                'error' => "Account status is {$user->account_status}."
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your account is currently {$user->account_status}. Please contact support for assistance.",
            ], 403);
        }

        // Check employment status for employees
        if ($userType == 'admin_employee' && $user->employment_status !== 'active') {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Verification Failed', $request->username, false, json_encode([
                'error' => "Employment status is {$user->employment_status}."
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your employment status is {$user->employment_status}. Please contact support.",
            ], 403);
        }

        // Check if the account start date is in the future
        if ($user->start_date > now()) {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Verification Failed', $request->username, false, json_encode([
                'error' => "Attempted login before activation date.",
                'start_date' => $user->start_date
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your account will be activated on {$user->start_date->format('Y-m-d')}. Please try again after this date.",
            ], 403);
        }

        // Check OTP expiration
        if ($user->login_otp_expires_at < now()) {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Expired', $request->username, false, json_encode([
                'error' => 'OTP has expired.',
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => 'The OTP has expired. Please request a new OTP and try again.',
            ], 401);
        }

        // Check OTP validity
        if (!$user->verifyTwoFactorLoginCode((string)$request->login_otp)) {

            $loginLogModel::logAttempt($user, 'Authentication', 'Invalid OTP', $request->username, false, json_encode([
                'error' => 'Incorrect OTP entered.',
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => 'The OTP you entered is incorrect. Please check and try again.',
            ], 401);
        }

        $user->resetTwoFactorLoginCode();

        if ($request->has('remember_my_choice')) {
            $credentials = json_encode(['username' => $request->username, 'password' => $request->password]);
            Cookie::queue('admin_credentials', $credentials, 525600);  // 1 year in minutes
        } else {
            Cookie::queue(Cookie::forget('admin_credentials'));
        }

        // Successful OTP verification and login
        $loginLogModel::logAttempt($user, 'Authentication', '2FA OTP Verified & Login Successful', $request->username, true, json_encode([
            'message' => 'User successfully logged in using OTP verification.'
        ]), $request->latitude, $request->longitude, $loginMethod);

        Auth::guard($userType)->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successful! Redirecting to your dashboard.',
            'redirect_url' => route('admin.dashboard'),
        ], 200);
    }

    public function resendOtp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'username.required' => 'Please enter your email, phone number, or username.',
            'latitude.required' => 'Location data is required.',
            'longitude.required' => 'Location data is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        // Determine login method
        $isEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL) !== false;
        $isMobile = preg_match('/^\+?[1-9]\d{1,14}$/', $request->username) === 1;
        $loginMethod = $isEmail ? 'email' : ($isMobile ? 'mobile' : 'username');

        // Find user
        $admin = Admin::findByUsername($request->username)->first();
        // $employee = AdminEmployee::findByUsername($request->username)->first();
        $user = $admin ?? $employee ?? null;
        // $userType = $admin ? 'admin' : ($employee ? 'admin_employee' : null);
        $userType = $admin ? 'admin' : null;

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No account found. Please check your credentials.',
            ], 404);
        }

        // Choose the appropriate login log model based on user type
        // $loginLogModel = $userType === 'admin_employee' ? AdminEmployeeLoginLog::class : AdminLoginLog::class;
        $loginLogModel = $userType === 'admin' ? AdminLoginLog::class : null;

        // Check if 2FA is enabled
        if (!$user->is_two_factor_enabled) {
            $loginLogModel::logAttempt($user, 'Authentication', '2FA Not Enabled', $request->username, false, json_encode([
                'error' => 'Two-Factor Authentication is not enabled.',
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => 'Two-Factor Authentication is not enabled for your account. Please contact support.',
            ], 403);
        }

        // Check account status
        if (strtolower($user->account_status) !== 'active') {

            $loginLogModel::logAttempt($user, 'Authentication', 'Account Inactive', $request->username, false, json_encode([
                'error' => "Account status is {$user->account_status}.",
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your account is currently {$user->account_status}. Please contact support.",
            ], 403);
        }

        // Check if employment status is active for employees
        if ($userType == 'admin_employee' && $user->employment_status !== 'active') {

            $loginLogModel::logAttempt($user, 'Authentication', 'Employment Inactive', $request->username, false, json_encode([
                'error' => "Employment status is {$user->employment_status}.",
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your employment status is {$user->employment_status}. Please contact support.",
            ], 403);
        }

        // Check if the start date is in the future
        if ($user->start_date > now()) {

            $loginLogModel::logAttempt($user, 'Authentication', 'Login Before Activation', $request->username, false, json_encode([
                'error' => 'Attempted login before activation date.',
                'start_date' => $user->start_date
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => "Your account will be activated on {$user->start_date->format('Y-m-d')}. Please try again later.",
            ], 403);
        }

        // Check if an OTP is still valid
        if ($user->login_otp_expires_at > now()) {

            $loginLogModel::logAttempt($user, 'Authentication', 'OTP Still Valid', $request->username, false, json_encode([
                'error' => 'Existing OTP is still valid.',
            ]), $request->latitude, $request->longitude, $loginMethod);

            return response()->json([
                'status' => false,
                'message' => 'Your current OTP is still valid. Please use it before requesting a new one.',
            ], 422);
        }

        // Resend OTP
        return $this->handleTwoFactorAuth($user, $userType, $request->latitude, $request->longitude, $loginMethod);
    }

    /**
     * Logout admin guard
     *
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        // Auth::guard('admin_employee')->logout();
        return redirect()->route('admin.auth.login');
    }
}
