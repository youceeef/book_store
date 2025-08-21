<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated
        if (!Auth::check()) {
            // Not logged in, redirect to login
            return redirect()->route('login');
        }

        // 2. Check if authenticated user is an admin
        // Ensure the 'is_admin' field exists and is true (or 1)
        if (!Auth::user()->is_admin) {
            // Not an admin, abort with 403 Forbidden
            // Or redirect them somewhere else, e.g., home or dashboard
            // abort(403, 'Unauthorized action.');
            return redirect()->route('home')->with('error', 'You do not have permission to access this area.');
        }

        // User is authenticated and is an admin, proceed with the request
        return $next($request);
    }
}
