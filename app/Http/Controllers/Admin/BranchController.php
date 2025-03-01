<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
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
}
