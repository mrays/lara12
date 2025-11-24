<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check if user has the required role
        if (!$this->hasRole($user, $role)) {
            // If trying to access admin area without admin role
            if ($role === 'admin') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            
            // For other roles, redirect to appropriate dashboard
            return $this->redirectToUserDashboard($user);
        }

        return $next($request);
    }

    /**
     * Check if user has the required role
     */
    private function hasRole($user, string $requiredRole): bool
    {
        // If user doesn't have role column, assume they're client
        if (!isset($user->role)) {
            return $requiredRole === 'client';
        }

        // Direct role comparison
        if ($user->role === $requiredRole) {
            return true;
        }

        // Admin can access client areas too
        if ($user->role === 'admin' && $requiredRole === 'client') {
            return true;
        }

        return false;
    }

    /**
     * Redirect user to appropriate dashboard based on their role
     */
    private function redirectToUserDashboard($user)
    {
        $role = $user->role ?? 'client';

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'client':
            default:
                return redirect()->route('client.dashboard');
        }
    }
}
