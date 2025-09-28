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
    Route::middleware(['auth:student', 'student.active'])->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StudentAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [StudentAuthController::class, 'changePassword'])->name('password.change');
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
        Route::get('/fees', [StudentController::class, 'fees'])->name('fees.index');
        Route::get('/payments', [StudentController::class, 'payments'])->name('payments.index');
        Route::get('/payments/history', [StudentController::class, 'paymentHistory'])->name('payments.history');
        Route::get('/payments/{payment}', [StudentController::class, 'paymentDetails'])->name('payments.show');
        Route::get('/statements', [StudentController::class, 'statements'])->name('statements.index');
        Route::get('/statements/download', [StudentController::class, 'downloadStatement'])->name('statements.download');
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
        Route::get('/payments/create', [StudentPaymentController::class, 'create'])
    ->name('payments.create');

        // Notifications
        Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [StudentController::class, 'markNotificationRead'])->name('notifications.read');
        
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
            Route::get('/{feeStructure}', [AdminController::class, 'showFeeStructure'])->name('show'); // Add this line
            Route::get('/{feeStructure}/edit', [AdminController::class, 'editFeeStructure'])->name('edit');
            Route::put('/{feeStructure}', [AdminController::class, 'updateFeeStructure'])->name('update');
            Route::delete('/{feeStructure}', [AdminController::class, 'destroyFeeStructure'])->name('destroy');
            Route::post('/{feeStructure}/toggle-status', [AdminController::class, 'toggleFeeStructureStatus'])->name('toggle-status');
            Route::post('/{feeStructure}/copy', [AdminController::class, 'copyFeeStructure'])->name('copy');
            Route::get('/{feeStructure}/breakdown', [AdminController::class, 'getFeeBreakdown'])->name('breakdown');
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
