<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to login page for unauthenticated users
        if (!auth()->check()) {
            return $next($request);
        }

        // Check if user is not a User model instance
        if (!auth()->user() instanceof \App\Models\User) {
            abort(403, 'Access denied. Admin access only.');
        }

        $user = auth()->user();

        // Check if user has permission to access admin panel
        if (!$user->canAccessAdminPanel()) {
            // If user is a client without permission, redirect to client panel
            if ($user->type === UserType::CLIENT) {
                return redirect('/client/login')->with('error', 'You do not have permission to access the admin panel. Please contact an administrator if you need access.');
            }
            
            // For other user types, show 403
            abort(403, 'Access denied. You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}