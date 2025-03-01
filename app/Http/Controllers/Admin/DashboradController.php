<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboradController extends Controller
{
    public function dashboard(Request $request)
    {
        $hasPermissions = $request->user->permissions;
        $adminBranchCreate = $request->user->canPerform('Admin Branch', 'create');
        return view('admin.dashboard.index', [
            'user' => $request->user,
            'userType' => $request->userType,
            'hasPermissions' => $hasPermissions,
        ]);
    }
}
