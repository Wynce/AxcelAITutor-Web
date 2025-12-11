<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('adminLogin')->with('error', 'Please login to access this page.');
        }

        $admin = Auth::guard('admin')->user();

        // Super admin has access to everything
        if ($admin->isSuperAdmin()) {
            return $next($request);
        }

        // Check if admin has one of the required roles
        if (!empty($roles)) {
            $hasRole = false;
            foreach ($roles as $role) {
                if ($admin->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                abort(403, 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}

