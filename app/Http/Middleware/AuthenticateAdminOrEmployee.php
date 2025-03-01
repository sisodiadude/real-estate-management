<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticateAdminOrEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            $request->merge(['user' => Auth::guard('admin')->user(), 'userType' => 'Admin']);
            Log::warning("Loggin Found Admin.");
            return $next($request);
        }

        /*
        if (Auth::guard('admin_employee')->check()) {
            $request->merge(['user' => Auth::guard('admin_employee')->user(), 'userType' => 'Admin Employee']);
            Log::warning("Login found Admin Employee.");
            return $next($request);
        }
        */

        Log::warning("Unauthorized access attempt detected.");

        // If the request expects JSON, return a JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'redirect_url' => route('admin.auth.login'),
                'message' => 'Unauthorized access.'
            ], 401);
        }

        // Otherwise, redirect to the login page
        return redirect()->route('admin.auth.login')->with('error', 'Unauthorized access.');
    }
}
