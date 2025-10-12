<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Generic login route fallback
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:student')->group(function () {
        Route::get('/register', [StudentAuthController::class, 'create'])->name('register');
        Route::post('/register', [StudentAuthController::class, 'store']);
        Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login']);
        
        // Password reset routes
        Route::get('/forgot-password', [StudentAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [StudentAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [StudentAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [StudentAuthController::class, 'resetPassword'])->name('password.update');
    });

    // Authenticated student routes
    Route::middleware(['auth:student', 'student.active', 'web'])->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [StudentController::class, 'changePassword'])->name('password.change');
        Route::put('/profile/picture', [StudentController::class, 'updateProfilePicture'])->name('profile.picture.update');
        Route::delete('/profile/picture', [StudentController::class, 'removeProfilePicture'])->name('profile.picture.remove');
        Route::get('/enrollments/{enrollment}/fee-details', [StudentController::class, 'getFeeDetails'])
        ->name('enrollments.fee-details');
        Route::get('/enrollments/{enrollment}', [StudentController::class, 'showEnrollment'])->name('enrollments.show');

        Route::get('/statements/pdf', [StudentController::class, 'downloadStatement'])->name('statements.pdf');

        Route::post('/enrollments/{enrollment}/defer', [StudentController::class, 'deferEnrollment'])
    ->name('enrollments.defer');
    Route::post('/enrollments/{enrollment}/resume', [StudentController::class, 'resumeEnrollment'])
    ->name('enrollments.resume');


        // Student portal routes
        Route::get('/courses', [StudentController::class, 'courses'])->name('courses.index');
        Route::get('/enrollments', [StudentController::class, 'enrollments'])->name('enrollments.index');
        Route::get('/enroll', [StudentController::class, 'enroll'])->name('enroll');
        Route::post('/enrollments', [StudentController::class, 'storeEnrollment'])->name('enrollments.store');
        Route::get('/enrollment/success/{enrollment}', [StudentController::class, 'enrollmentSuccess'])->name('enrollment.success');
        Route::get('/enrollment/error', [StudentController::class, 'enrollmentError'])->name('enrollment.error');
        Route::get('/fees', [StudentController::class, 'fees'])->name('fees.index');
        Route::get('/payments', [StudentController::class, 'payments'])->name('payments.index');
        Route::get('/payments/history', [StudentController::class, 'paymentHistory'])->name('payments.history');
        Route::get('/payments/{payment}', [StudentController::class, 'paymentDetails'])->name('payments.show');
        Route::get('/statements', [StudentController::class, 'statements'])->name('statements.index');
        Route::get('/statements/download', [StudentController::class, 'downloadStatement'])->name('statements.download');
        Route::get('/statements/download-pdf', [StudentController::class, 'downloadStatementPDF'])->name('statements.download-pdf');
        Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
        Route::put('/settings', [StudentController::class, 'updateSettings'])->name('settings.update');
        Route::get('/statements/download', [StudentController::class, 'downloadStatement'])
    ->name('statements.download');

        // Payment processing routes
        Route::post('/payments/initiate', [StudentPaymentController::class, 'initiate'])->name('payments.initiate');
        Route::get('/payments/mpesa/callback', [StudentPaymentController::class, 'mpesaCallback'])->name('payments.mpesa.callback');
        Route::post('/payments/stripe/webhook', [StudentPaymentController::class, 'stripeWebhook'])->name('payments.stripe.webhook');
        Route::get('/payments/success', [StudentPaymentController::class, 'paymentSuccess'])->name('payments.success');
        Route::get('/payments/cancel', [StudentPaymentController::class, 'paymentCancel'])->name('payments.cancel');
        // Route moved above to avoid dependency issues
        
        // Test route
        Route::get('/test-payment', function() {
            return 'Payment route works!';
        });
        
        // Payment routes
        Route::get('/payments/create', [StudentPaymentController::class, 'create'])->name('payments.create');
        Route::get('/payment/create', [StudentPaymentController::class, 'create'])->name('payment.create');
        Route::post('/payments/initiate', [StudentPaymentController::class, 'initiate'])->name('payments.initiate');
        Route::get('/payments/success', [StudentPaymentController::class, 'success'])->name('payments.success');
        Route::get('/payments/cancel', [StudentPaymentController::class, 'cancel'])->name('payments.cancel');
        
        // Test route without middleware
        Route::get('/test-payment', function() {
            return 'Payment route works!';
        })->name('test.payment');

        // AI Assistant routes
        Route::post('/ai-assistant/get-assistance', [\App\Http\Controllers\Student\AIAssistantController::class, 'getAssistance'])->name('ai-assistant.get-assistance');
        Route::get('/ai-assistant/payment-insights', [\App\Http\Controllers\Student\AIAssistantController::class, 'getPaymentInsights'])->name('ai-assistant.payment-insights');
        
        // Notifications
        Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [StudentController::class, 'markNotificationRead'])->name('notifications.read');
        
        // Payment Plans Routes
        Route::get('/payment-plans', function() { 
            $student = auth('student')->user();
            $paymentPlans = \App\Models\PaymentPlan::where('student_id', $student->id)
                ->with(['enrollment.course', 'installments'])
                ->get();
            return view('student.payment-plans.index', compact('paymentPlans')); 
        })->name('payment-plans.index');
        Route::get('/payment-plans/create', function() { 
            $student = auth('student')->user();
            $enrollments = $student->enrollments()->with('course')->get();
            $enrollment = $enrollments->first(); // Default to first enrollment
            
            // Calculate outstanding balance if enrollment exists
            if ($enrollment) {
                $totalFee = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 100000);
                $paidAmount = $enrollment->fees_paid ?? 0;
                $enrollment->outstanding_balance = max(0, $totalFee - $paidAmount);
            }
            
            return view('student.payment-plans.create', compact('enrollments', 'enrollment')); 
        })->name('payment-plans.create');
        Route::post('/payment-plans', function(\Illuminate\Http\Request $request) { 
            $student = auth('student')->user();
            
            // Create payment plan
            $paymentPlan = \App\Models\PaymentPlan::create([
                'student_id' => $student->id,
                'student_enrollment_id' => $request->enrollment_id,
                'plan_name' => $request->plan_name,
                'total_amount' => $request->total_amount,
                'total_installments' => $request->total_installments,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create installments
            $installmentAmounts = $request->installment_amounts;
            $installmentDates = $request->installment_dates;
            
            for ($i = 0; $i < count($installmentAmounts); $i++) {
                \App\Models\PaymentInstallment::create([
                    'payment_plan_id' => $paymentPlan->id,
                    'installment_number' => $i + 1,
                    'amount' => $installmentAmounts[$i],
                    'due_date' => $installmentDates[$i],
                    'status' => 'pending'
                ]);
            }
            
            return redirect()->route('student.payment-plans.index')->with('success', 'Payment plan created successfully!'); 
        })->name('payment-plans.store');
        Route::get('/payment-plans/{id}', function($id) { 
            $paymentPlan = \App\Models\PaymentPlan::with(['enrollment.course', 'installments'])->findOrFail($id);
            return view('student.payment-plans.show', compact('paymentPlan')); 
        })->name('payment-plans.show');
        Route::get('/payment-plans/{id}/edit', function($id) { 
            $paymentPlan = collect(); // Placeholder
            return view('student.payment-plans.edit', compact('id', 'paymentPlan')); 
        })->name('payment-plans.edit');
        Route::put('/payment-plans/{id}', function($id) { return redirect()->route('student.payment-plans.index'); })->name('payment-plans.update');
        Route::delete('/payment-plans/{id}', function($id) { return redirect()->route('student.payment-plans.index'); })->name('payment-plans.destroy');

        // Import/Export Routes
        Route::post('/import', [StudentController::class, 'import'])->name('import');
        Route::get('/export', [StudentController::class, 'export'])->name('export');
        Route::get('/template', [StudentController::class, 'downloadTemplate'])->name('template');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware(['web', 'guest:admin'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
        
        // Password reset routes
        Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');
    });

    // Authenticated admin routes
    Route::middleware(['web', 'auth:admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AdminAuthController::class, 'changePassword'])->name('password.change');
        
        // Dashboard data routes
        Route::get('/dashboard/revenue-data', [AdminAuthController::class, 'getRevenueData'])->name('dashboard.revenue-data');
        
        // Student Management Routes (requires manage_students permission)
        Route::middleware('admin.auth:manage_students')->prefix('students')->name('students.')->group(function () {
            Route::get('/', [StudentManagementController::class, 'index'])->name('index');
            Route::get('/create', [StudentManagementController::class, 'create'])->name('create');
            Route::post('/', [StudentManagementController::class, 'store'])->name('store');
            Route::get('/{student}', [StudentManagementController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [StudentManagementController::class, 'edit'])->name('edit');
            Route::put('/{student}', [StudentManagementController::class, 'update'])->name('update');
            Route::delete('/{student}', [StudentManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{student}/approve', [StudentManagementController::class, 'approve'])->name('approve');
            Route::post('/{student}/suspend', [StudentManagementController::class, 'suspend'])->name('suspend');
            Route::post('/{student}/activate', [StudentManagementController::class, 'activate'])->name('activate');
            Route::get('/{student}/payments', [StudentManagementController::class, 'payments'])->name('payments');
            Route::get('/{student}/enrollments', [StudentManagementController::class, 'enrollments'])->name('enrollments');
            Route::post('/bulk-update', [StudentManagementController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export', [StudentManagementController::class, 'export'])->name('export');
            Route::post('/import', [StudentManagementController::class, 'import'])->name('import');
            Route::get('/enrollments/{enrollment}/fee-details', [StudentController::class, 'getFeeDetails'])
            ->name('enrollments.fee-details');
        });
        
        // Course Management Routes (requires manage_courses permission)
        Route::middleware('admin.auth:manage_courses')->prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');
            Route::get('/create', [CourseController::class, 'create'])->name('create');
            Route::post('/', [CourseController::class, 'store'])->name('store');
            Route::get('/{course}', [CourseController::class, 'show'])->name('show');
            Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
            Route::put('/{course}', [CourseController::class, 'update'])->name('update');
            Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
            Route::post('/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{course}/students', [CourseController::class, 'students'])->name('students');
            Route::get('/{course}/fee-structures', [CourseController::class, 'feeStructures'])->name('fee-structures');
            Route::put('/{course}/update-fees', [CourseController::class, 'updateFees'])->name('update-fees');
            Route::post('/{course}/duplicate', [CourseController::class, 'duplicate'])->name('duplicate');
            Route::post('/bulk-update', [CourseController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export', [CourseController::class, 'export'])->name('export');
            Route::get('/{course}/stats', [CourseController::class, 'getStats'])->name('stats');
        });
        
        // Semester Management Routes (requires manage_courses permission)
        Route::middleware('admin.auth:manage_courses')->prefix('semesters')->name('semesters.')->group(function () {
            Route::get('/', [SemesterController::class, 'index'])->name('index');
            Route::get('/create', [SemesterController::class, 'create'])->name('create');
            Route::post('/', [SemesterController::class, 'store'])->name('store');
            Route::get('/{semester}', [SemesterController::class, 'show'])->name('show');
            Route::get('/{semester}/edit', [SemesterController::class, 'edit'])->name('edit');
            Route::put('/{semester}', [SemesterController::class, 'update'])->name('update');
            Route::delete('/{semester}', [SemesterController::class, 'destroy'])->name('destroy');
            Route::post('/{semester}/toggle-status', [SemesterController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{semester}/enrollments', [SemesterController::class, 'enrollments'])->name('enrollments');
            Route::post('/{semester}/duplicate', [SemesterController::class, 'duplicate'])->name('duplicate');
            Route::post('/bulk-update', [SemesterController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export', [SemesterController::class, 'export'])->name('export');
            Route::get('/{semester}/stats', [SemesterController::class, 'getStats'])->name('stats');
            Route::get('/{semester}/registration-status', [SemesterController::class, 'checkRegistrationStatus'])->name('registration-status');
        });
        
        // Payment Management Routes (requires manage_payments permission)
        Route::middleware('admin.auth:manage_payments')->prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
            Route::get('/create', [AdminPaymentController::class, 'create'])->name('create');
            Route::post('/', [AdminPaymentController::class, 'store'])->name('store');
            Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('verify');
            Route::post('/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
            Route::get('/pending', [AdminPaymentController::class, 'pending'])->name('pending');
            Route::get('/failed', [AdminPaymentController::class, 'failed'])->name('failed');
            Route::post('/bulk-verify', [AdminPaymentController::class, 'bulkVerify'])->name('bulk-verify');
            Route::post('/bulk-update', [AdminPaymentController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export', [AdminPaymentController::class, 'export'])->name('export');
            Route::post('/{payment}/retry', [AdminPaymentController::class, 'retry'])->name('retry');
            Route::post('/manual-entry', [AdminPaymentController::class, 'manualEntry'])->name('manual-entry');
        });
        
        // Fee Management Routes (requires manage_fees permission)
        Route::middleware('admin.auth:manage_fees')->prefix('fee-structures')->name('fee-structures.')->group(function () {
            Route::get('/', [AdminController::class, 'feeStructures'])->name('index');
            Route::get('/create', [AdminController::class, 'createFeeStructure'])->name('create');
            Route::post('/', [AdminController::class, 'storeFeeStructure'])->name('store');
            Route::get('/{feeStructure}', [AdminController::class, 'showFeeStructure'])->name('show');
            Route::get('/{feeStructure}/edit', [AdminController::class, 'editFeeStructure'])->name('edit');
            Route::put('/{feeStructure}', [AdminController::class, 'updateFeeStructure'])->name('update');
            Route::delete('/{feeStructure}', [AdminController::class, 'destroyFeeStructure'])->name('destroy');
            Route::post('/{feeStructure}/toggle-status', [AdminController::class, 'toggleFeeStructureStatus'])->name('toggle-status');
            Route::post('/{feeStructure}/copy', [AdminController::class, 'copyFeeStructure'])->name('copy');
            Route::get('/{feeStructure}/breakdown', [AdminController::class, 'getFeeBreakdown'])->name('breakdown');
        });
        
        // Search Routes
        Route::prefix('search')->name('search.')->group(function () {
            Route::get('/advanced', function() { return view('admin.search.advanced'); })->name('advanced');
            Route::get('/global', [\App\Http\Controllers\Admin\SearchController::class, 'globalSearch'])->name('global');
            Route::post('/students', [\App\Http\Controllers\Admin\SearchController::class, 'studentSearch'])->name('students');
            Route::post('/payment-filters', [\App\Http\Controllers\Admin\SearchController::class, 'paymentFilters'])->name('payment-filters');
            Route::get('/student-lookup', [\App\Http\Controllers\Admin\SearchController::class, 'studentLookup'])->name('student-lookup');
            Route::post('/transactions', [\App\Http\Controllers\Admin\SearchController::class, 'transactionSearch'])->name('transactions');
        });
        
        // AI Analytics Routes (requires view_reports permission)
        Route::middleware('admin.auth:view_reports')->prefix('ai-analytics')->name('ai-analytics.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'getAnalyticsData'])->name('data');
            Route::get('/fraud-detection', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'fraudDetection'])->name('fraud-detection');
            Route::get('/payment-behavior', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'paymentBehavior'])->name('payment-behavior');
            Route::get('/support-dashboard', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'supportDashboard'])->name('support-dashboard');
            Route::post('/generate-response', [\App\Http\Controllers\Admin\AIAnalyticsController::class, 'generateResponse'])->name('generate-response');
        });
        
        // Reports Routes (requires view_reports permission)
        Route::middleware('admin.auth:view_reports')->prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('/students', [ReportController::class, 'students'])->name('students');
            Route::get('/courses', [ReportController::class, 'courses'])->name('courses');
            Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
            Route::get('/export/payments', [ReportController::class, 'exportPayments'])->name('export.payments');
            Route::get('/export/students', [ReportController::class, 'exportStudents'])->name('export.students');
            Route::get('/export/financial', [ReportController::class, 'exportFinancial'])->name('export.financial');
        });
        
        // Super Admin Routes (requires super_admin permission)
        Route::middleware('admin.auth:super_admin')->group(function () {
            Route::get('/settings', [AdminAuthController::class, 'systemSettings'])->name('settings.index');
            Route::get('/settings/account', [AdminAuthController::class, 'accountSettings'])->name('settings.account');
            Route::get('/admins', [AdminAuthController::class, 'adminManagement'])->name('admins.index');
            Route::post('/admins', [AdminAuthController::class, 'createAdmin'])->name('admins.create');
            Route::put('/admins/{admin}', [AdminAuthController::class, 'updateAdminPermissions'])->name('admins.update');
            
            // System maintenance routes
            Route::post('/maintenance/clear-cache', [AdminAuthController::class, 'clearCache'])->name('maintenance.clear-cache');
            Route::post('/maintenance/clear-routes', [AdminAuthController::class, 'clearRoutes'])->name('maintenance.clear-routes');
            Route::post('/maintenance/clear-views', [AdminAuthController::class, 'clearViews'])->name('maintenance.clear-views');
            Route::post('/maintenance/optimize', [AdminAuthController::class, 'optimizeApp'])->name('maintenance.optimize');
            Route::post('/maintenance/migrate', [AdminAuthController::class, 'runMigrations'])->name('maintenance.migrate');
            Route::post('/maintenance/seed', [AdminAuthController::class, 'seedDatabase'])->name('maintenance.seed');
        });
    });
});

// Fallback login route for Laravel's default auth redirects
Route::get('/login', function () {
    // Check if request is from admin area
    if (request()->is('admin/*') || str_contains(request()->headers->get('referer', ''), '/admin')) {
        return redirect()->route('admin.login');
    }
    // Default to student login
    return redirect()->route('student.login');
})->name('login');

// Public webhook routes (no authentication required)
Route::post('/webhooks/mpesa', [StudentPaymentController::class, 'mpesaCallback'])->name('webhooks.mpesa');
Route::post('/webhooks/stripe', [StudentPaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [StudentPaymentController::class, 'paypalWebhook'])->name('webhooks.paypal');

// Legal pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

// Test route without any middleware
Route::get('/test-public', function() {
    return 'Public route works!';
});

// Include payment routes if file exists
if (file_exists(__DIR__.'/payment.php')) {
    require __DIR__.'/payment.php';
}

// M-Pesa webhook routes (no middleware needed)
Route::post('/webhooks/mpesa', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('webhooks.mpesa');
Route::post('/api/mpesa/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('api.mpesa.callback');
Route::get('/api/mpesa/callback', function() {
    return response()->json(['status' => 'M-Pesa callback endpoint active', 'time' => now()]);
});
Route::post('/webhooks/mpesa/timeout', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('webhooks.mpesa.timeout');
Route::post('/webhooks/mpesa/result', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('webhooks.mpesa.result');