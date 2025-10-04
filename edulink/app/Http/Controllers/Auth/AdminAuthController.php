<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function showLoginForm(): View
    {
        return view('auth.admin.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();
            
            // Check if admin account is active
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact system administrator.',
                ]);
            }

            // Update last login
            $admin->update(['last_login_at' => now()]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        $admin = Auth::guard('admin')->user();
        
        // Get dashboard statistics
        $currentMonth = now();
        $lastMonth = now()->subMonth();
        
        // Calculate outstanding balances from enrollments
        $totalOutstanding = \App\Models\StudentEnrollment::sum('total_fees_due') - \App\Models\StudentEnrollment::sum('fees_paid');
        $overdueEnrollments = \App\Models\StudentEnrollment::where('next_payment_due', '<', now())
            ->where('fees_paid', '<', \DB::raw('total_fees_due'))
            ->get();
        $overdueAmount = $overdueEnrollments->sum('outstanding_balance');
        
        $stats = [];
        
        if ($admin->canManageStudents()) {
            $stats['total_students'] = Student::count();
            $stats['active_students'] = Student::where('status', 'active')->count();
            $stats['pending_students'] = Student::where('status', 'pending_verification')->count();
            $stats['suspended_students'] = Student::where('status', 'suspended')->count();
            $stats['new_students_this_month'] = Student::whereYear('created_at', $currentMonth->year)
                ->whereMonth('created_at', $currentMonth->month)
                ->count();
        }
        
        if ($admin->canManageCourses()) {
            $stats['total_courses'] = Course::count();
            $stats['active_courses'] = Course::where('is_active', true)->count();
            $stats['total_semesters'] = Semester::count();
            $stats['active_semesters'] = Semester::where('status', 'active')->count();
        }
        
        if ($admin->canManagePayments() || $admin->canViewReports()) {
            $stats['total_revenue'] = Payment::where('status', 'completed')->sum('amount');
        }
        
        if ($admin->canManagePayments() || $admin->canManageFees()) {
            $stats['total_outstanding'] = $totalOutstanding;
            $stats['pending_payments'] = Payment::where('status', 'pending')->count();
            $stats['pending_amount'] = Payment::where('status', 'pending')->sum('amount');
        }
        
        if ($admin->canManagePayments()) {
            $stats['overdue_payments'] = $overdueEnrollments->count();
            $stats['overdue_amount'] = $overdueAmount;
        }

        // Calculate revenue growth percentage
        $currentMonthRevenue = Payment::where('status', 'completed')
            ->whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->sum('amount');
            
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->sum('amount');
            
        $stats['revenue_growth_percentage'] = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        // Payment statistics
        $paymentStats = [
            'total_payments' => Payment::sum('amount'),
            'completed_payments' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'recent_payments_count' => Payment::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Recent activities based on permissions
        $recentPayments = collect();
        if ($admin->canManagePayments()) {
            $recentPayments = Payment::with(['student', 'enrollment.course'])
                ->latest()
                ->take(10)
                ->get();
        }

        $recentStudents = collect();
        if ($admin->canManageStudents()) {
            $recentStudents = Student::with('activeEnrollments.course')
                ->latest()
                ->take(5)
                ->get();
        }

        // Pending approvals (if admin has permission)
        $pendingApprovals = [];
        if ($admin->can_approve_students) {
            $pendingApprovals = Student::where('status', 'pending_verification')
                ->with('activeEnrollments.course')
                ->latest()
                ->take(5)
                ->get();
        }

        // Monthly payment trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'amount' => Payment::where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
                'count' => Payment::where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // Chart data for dashboard charts
        $chartData = [
            'revenue' => [
                'labels' => collect($monthlyTrends)->pluck('month')->toArray(),
                'data' => collect($monthlyTrends)->pluck('amount')->toArray(),
            ],
            'paymentMethods' => [
                'labels' => ['M-Pesa', 'Stripe', 'Bank Transfer', 'Cash', 'Other'],
                'data' => [
                    Payment::where('status', 'completed')->where('payment_method', 'mpesa')->count(),
                    Payment::where('status', 'completed')->where('payment_method', 'stripe')->count(),
                    Payment::where('status', 'completed')->where('payment_method', 'bank_transfer')->count(),
                    Payment::where('status', 'completed')->where('payment_method', 'cash')->count(),
                    Payment::where('status', 'completed')->whereNotIn('payment_method', ['mpesa', 'stripe', 'bank_transfer', 'cash'])->count(),
                ]
            ]
        ];

        // System alerts
        $alerts = collect();
        
        // Add alert for pending student approvals
        if ($admin->can_approve_students && isset($stats['pending_students']) && $stats['pending_students'] > 0) {
            $alerts->push([
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'Pending Approvals',
                'message' => "You have {$stats['pending_students']} students waiting for approval."
            ]);
        }
        
        // Add alert for overdue payments
        if (isset($stats['overdue_payments']) && $stats['overdue_payments'] > 0) {
            $alerts->push([
                'type' => 'danger',
                'icon' => 'clock-history',
                'title' => 'Overdue Payments',
                'message' => "There are {$stats['overdue_payments']} overdue payments totaling KSh " . number_format($stats['overdue_amount'], 2) . "."
            ]);
        }
        
        // Add alert for failed payments
        if ($paymentStats['failed_payments'] > 5) {
            $alerts->push([
                'type' => 'warning',
                'icon' => 'x-circle',
                'title' => 'Failed Payments',
                'message' => "There are {$paymentStats['failed_payments']} failed payment attempts that may need attention."
            ]);
        }

        return view('admin.dashboard', compact(
            'admin',
            'stats',
            'paymentStats',
            'recentPayments',
            'recentStudents',
            'pendingApprovals',
            'monthlyTrends',
            'chartData',
            'alerts'
        ));
    }

    /**
     * Display admin profile
     */
    public function profile(): View
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:admins,phone,' . $admin->id],
        ]);

        $admin->update($request->only([
            'first_name',
            'last_name',
            'phone'
        ]));

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Auth::guard('admin')->user();
        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password changed successfully.');
    }

    /**
     * Display system settings (Super Admin only)
     */
    public function systemSettings(): View
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        $settings = [
            'payment_methods' => config('services.payment_methods', []),
            'mpesa_settings' => config('services.mpesa', []),
            'stripe_settings' => config('services.stripe', []),
            'notification_settings' => config('services.notifications', []),
        ];

        return view('admin.settings', compact('admin', 'settings'));
    }

    /**
     * Display admin management (Super Admin only)
     */
    public function adminManagement(): View
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        $admins = Admin::latest()->paginate(20);

        return view('admin.admin-management', compact('admin', 'admins'));
    }

    /**
     * Create new admin (Super Admin only)
     */
    public function createAdmin(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Admin::class],
            'phone' => ['nullable', 'string', 'max:20', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,finance,registrar,super_admin'],
            'permissions' => ['array'],
        ]);

        $newAdmin = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,

            'can_manage_students' => in_array('manage_students', $request->permissions ?? []),
            'can_manage_courses' => in_array('manage_courses', $request->permissions ?? []),
            'can_manage_payments' => in_array('manage_payments', $request->permissions ?? []),
            'can_view_reports' => in_array('view_reports', $request->permissions ?? []),
            'can_approve_students' => in_array('approve_students', $request->permissions ?? []),
            'can_manage_fees' => in_array('manage_fees', $request->permissions ?? []),
            'is_active' => true,
        ]);

        return redirect()->route('admin.admin-management')->with('success', 'Admin created successfully.');
    }

    /**
     * Update admin permissions (Super Admin only)
     */
    public function updateAdminPermissions(Request $request, Admin $targetAdmin): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        $request->validate([
            'permissions' => ['array'],
            'is_active' => ['boolean'],
        ]);

        $targetAdmin->update([
            'can_manage_students' => in_array('manage_students', $request->permissions ?? []),
            'can_manage_courses' => in_array('manage_courses', $request->permissions ?? []),
            'can_manage_payments' => in_array('manage_payments', $request->permissions ?? []),
            'can_view_reports' => in_array('view_reports', $request->permissions ?? []),
            'can_approve_students' => in_array('approve_students', $request->permissions ?? []),
            'can_manage_fees' => in_array('manage_fees', $request->permissions ?? []),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.admin-management')->with('success', 'Admin permissions updated successfully.');
    }
}
