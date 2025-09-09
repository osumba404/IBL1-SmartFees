<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();

        // Check if admin account is active
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'message' => 'Your account has been deactivated. Please contact system administrator.'
            ]);
        }

        // Check permissions if specified
        if (!empty($permissions)) {
            $hasPermission = false;

            foreach ($permissions as $permission) {
                switch ($permission) {
                    case 'super_admin':
                        $hasPermission = $admin->is_super_admin;
                        break;
                    case 'manage_students':
                        $hasPermission = $admin->can_manage_students || $admin->is_super_admin;
                        break;
                    case 'manage_courses':
                        $hasPermission = $admin->can_manage_courses || $admin->is_super_admin;
                        break;
                    case 'manage_payments':
                        $hasPermission = $admin->can_manage_payments || $admin->is_super_admin;
                        break;
                    case 'view_reports':
                        $hasPermission = $admin->can_view_reports || $admin->is_super_admin;
                        break;
                    case 'approve_students':
                        $hasPermission = $admin->can_approve_students || $admin->is_super_admin;
                        break;
                    case 'manage_fees':
                        $hasPermission = $admin->can_manage_fees || $admin->is_super_admin;
                        break;
                    default:
                        $hasPermission = false;
                }

                if ($hasPermission) {
                    break;
                }
            }

            if (!$hasPermission) {
                abort(403, 'Access denied. Insufficient permissions.');
            }
        }

        return $next($request);
    }
}
