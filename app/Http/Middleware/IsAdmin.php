<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
            }
            abort(403, 'Akses ditolak: Admin saja.');
        }

        return $next($request);
    }
}