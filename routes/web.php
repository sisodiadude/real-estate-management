<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\CountryController;
use App\Http\Controllers\Common\StateController;
use App\Http\Controllers\Common\CityController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboradController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================
// Country Management
// ========================
Route::prefix('countries')->name('countries.')->group(function () {
    Route::post('get-all', [CountryController::class, 'getAll'])->name('getAll'); // Fetch all countries
});

// ========================
// State Management
// ========================
Route::prefix('states')->name('states.')->group(function () {
    // State Management (Professional RESTful Route)
    Route::get('country/{country}', [StateController::class, 'index'])->name('index');
});

// ========================
// City Management
// ========================
Route::prefix('cities')->name('cities.')->group(function () {
    // State Management (Professional RESTful Route)
    Route::get('state/{state}', [CityController::class, 'index'])->name('index');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication Routes
    Route::name('auth.')->group(function () {
        // Authentication Views
        Route::get('login', [AdminAuthController::class, 'index'])->name('login');  // Admin login page
        Route::view('register', 'admin.auth.register')->name('register'); // Admin register page

        // Authentication Actions
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
        Route::post('register', [AdminAuthController::class, 'register'])->name('register.submit');
        Route::post('resend-otp', [AdminAuthController::class, 'resendOtp'])->name('resend_otp.submit');
        Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp'])->name('verify_otp.submit');
        Route::post('forget-password', [AdminAuthController::class, 'forgetPassword'])->name('forgot_password.submit');
    });

    // ========================
    // Admin Protected Routes (Requires 'auth:admin' Middleware)
    // ========================
    // ->middleware('auth:admin')
    Route::middleware(['auth.admin_or_employee'])->group(function () {
        // Dashboard or other admin routes can be added here
        Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // ========================
        // Branch Management
        // ========================
        Route::prefix('branches')->name('branches.')->group(function () {

            // Branch CRUD
            Route::get('create', [AdminBranchController::class, 'create'])->name('create'); // Create branch form
            Route::post('store', [AdminBranchController::class, 'store'])->name('store'); // Store branch
            Route::get('/', [AdminBranchController::class, 'index'])->name('index'); // List all branches
            Route::get('data', [AdminBranchController::class, 'getBranches'])->name('getBranches');

            // Soft Deleted Branches (Moved ABOVE `show` to avoid conflicts)
            Route::get('trash', [AdminBranchController::class, 'trash'])->name('trash'); // View soft-deleted branches
            Route::get('trash/data', [AdminBranchController::class, 'getTrashedBranches'])->name('trash.data'); // AJAX DataTables for soft-deleted branches

            // Branch Details
            Route::get('{branchSlug}/edit', [AdminBranchController::class, 'edit'])->name('edit'); // Edit branch form
            Route::put('{branchSlug}/edit', [AdminBranchController::class, 'update'])->name('update'); // Update branch
            Route::delete('{branchSlug}', [AdminBranchController::class, 'delete'])->name('delete'); // Delete branch

            // Prevent "trash" from being treated as a branch slug
            Route::get('{branchSlug}', [AdminBranchController::class, 'show'])->name('show'); // Show full branch details

            // Restore & Destroy Soft Deleted Branches
            Route::post('{branchSlug}/restore', [AdminBranchController::class, 'restore'])->name('restore'); // Restore soft-deleted branch
            Route::delete('{branchSlug}/destroy', [AdminBranchController::class, 'destroy'])->name('destroy'); // Permanently delete branch

            // ========================
            // Department Management
            // ========================
            Route::prefix('{branchSlug}/departments')->name('departments.')->group(function () {

                // Branch CRUD
                Route::get('create', [AdminDepartmentController::class, 'create'])->name('create'); // Create department form
                Route::post('store', [AdminDepartmentController::class, 'store'])->name('store'); // Store department
                Route::get('/', [AdminDepartmentController::class, 'index'])->name('index'); // List all departments
                Route::get('data', [AdminDepartmentController::class, 'getDepartments'])->name('getDepartments');

                // Soft Deleted Departments (Moved ABOVE `show` to avoid conflicts)
                Route::get('trash', [AdminDepartmentController::class, 'trash'])->name('trash'); // View soft-deleted departments
                Route::get('trash/data', [AdminDepartmentController::class, 'getTrashedDepartments'])->name('trash.data'); // AJAX DataTables for soft-deleted departments

                // Department Details
                Route::get('{departmentSlug}/edit', [AdminDepartmentController::class, 'edit'])->name('edit'); // Edit department form
                Route::put('{departmentSlug}/edit', [AdminDepartmentController::class, 'update'])->name('update'); // Update department
                Route::delete('{departmentSlug}', [AdminDepartmentController::class, 'delete'])->name('delete'); // Delete department

                // Prevent "trash" from being treated as a department slug
                Route::get('{departmentSlug}', [AdminDepartmentController::class, 'show'])->name('show'); // Show full department details

                // Restore & Destroy Soft Deleted Departments
                Route::post('{departmentSlug}/restore', [AdminDepartmentController::class, 'restore'])->name('restore'); // Restore soft-deleted department
                Route::delete('{departmentSlug}/destroy', [AdminDepartmentController::class, 'destroy'])->name('destroy'); // Permanently delete department

                // ========================
                // Team Management
                // ========================
                Route::prefix('{departmentSlug}/teams')->name('teams.')->group(function () {

                    // Branch CRUD
                    Route::get('create', [AdminTeamController::class, 'create'])->name('create'); // Create department form
                    Route::post('store', [AdminTeamController::class, 'store'])->name('store'); // Store department
                    Route::get('/', [AdminTeamController::class, 'index'])->name('index'); // List all departments
                    Route::get('data', [AdminTeamController::class, 'getTeams'])->name('getTeams');

                    // Soft Deleted Departments (Moved ABOVE `show` to avoid conflicts)
                    Route::get('trash', [AdminTeamController::class, 'trash'])->name('trash'); // View soft-deleted departments
                    Route::get('trash/data', [AdminTeamController::class, 'getTrashedTeams'])->name('trash.data'); // AJAX DataTables for soft-deleted departments

                    // Department Details
                    Route::get('{teamSlug}/edit', [AdminTeamController::class, 'edit'])->name('edit'); // Edit department form
                    Route::put('{teamSlug}/edit', [AdminTeamController::class, 'update'])->name('update'); // Update department
                    Route::delete('{teamSlug}', [AdminTeamController::class, 'delete'])->name('delete'); // Delete department

                    // Prevent "trash" from being treated as a department slug
                    Route::get('{teamSlug}', [AdminTeamController::class, 'show'])->name('show'); // Show full department details

                    // Restore & Destroy Soft Deleted Departments
                    Route::post('{teamSlug}/restore', [AdminTeamController::class, 'restore'])->name('restore'); // Restore soft-deleted department
                    Route::delete('{teamSlug}/destroy', [AdminTeamController::class, 'destroy'])->name('destroy'); // Permanently delete department

                    // ========================
                    // Employee Management
                    // ========================
                    Route::prefix('{teamSlug}/employees')->name('employees.')->group(function () {

                        // Branch CRUD
                        Route::get('create', [AdminTeamController::class, 'create'])->name('create'); // Create department form
                        Route::post('store', [AdminTeamController::class, 'store'])->name('store'); // Store department
                        Route::get('/', [AdminTeamController::class, 'index'])->name('index'); // List all departments
                        Route::get('data', [AdminTeamController::class, 'getTeams'])->name('getTeams');

                        // Soft Deleted Departments (Moved ABOVE `show` to avoid conflicts)
                        Route::get('trash', [AdminTeamController::class, 'trash'])->name('trash'); // View soft-deleted departments
                        Route::get('trash/data', [AdminTeamController::class, 'getTrashedTeams'])->name('trash.data'); // AJAX DataTables for soft-deleted departments

                        // Department Details
                        Route::get('{employeeSlug}/edit', [AdminTeamController::class, 'edit'])->name('edit'); // Edit department form
                        Route::put('{employeeSlug}/edit', [AdminTeamController::class, 'update'])->name('update'); // Update department
                        Route::delete('{employeeSlug}', [AdminTeamController::class, 'delete'])->name('delete'); // Delete department

                        // Prevent "trash" from being treated as a department slug
                        Route::get('{employeeSlug}', [AdminTeamController::class, 'show'])->name('show'); // Show full department details

                        // Restore & Destroy Soft Deleted Departments
                        Route::post('{employeeSlug}/restore', [AdminTeamController::class, 'restore'])->name('restore'); // Restore soft-deleted department
                        Route::delete('{employeeSlug}/destroy', [AdminTeamController::class, 'destroy'])->name('destroy'); // Permanently delete department
                    });
                });
            });
        });

        // Logout
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('auth.logout'); // Admin Logout

    });
});
