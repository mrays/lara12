<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Debug: Log user status
        if ($user) {
            \Log::info('User status check: ' . $user->email . ' - Status: ' . $user->status);
        }

        // Check if user exists and is inactive
        if ($user && ($user->status === 'INACTIVE' || $user->status === 'inactive' || $user->status === 0 || $user->status === '0')) {
            \Log::warning('Inactive user attempted access: ' . $user->email);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'], 403);
            }

            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
