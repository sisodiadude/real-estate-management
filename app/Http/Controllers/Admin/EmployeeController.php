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

// Laravel Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// HTTP Response Handling
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
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
}
