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
    return view('welcome');
});

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:student')->group(function () {
        Route::get('/register', [StudentAuthController::class, 'create'])->name('register');
        Route::post('/register', [StudentAuthController::class, 'store']);
        Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login']);
    });

    // Authenticated student routes
    Route::middleware('auth:student')->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StudentAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [StudentAuthController::class, 'changePassword'])->name('password.change');
        
        // Student portal routes
        Route::get('/courses', [StudentController::class, 'courses'])->name('courses');
        Route::get('/enrollments', [StudentController::class, 'enrollments'])->name('enrollments');
        Route::get('/fees', [StudentController::class, 'fees'])->name('fees');
        Route::get('/payments', [StudentController::class, 'payments'])->name('payments');
        Route::get('/payments/{payment}', [StudentController::class, 'paymentDetails'])->name('payments.show');
        Route::get('/statements', [StudentController::class, 'statements'])->name('statements');
        Route::get('/statements/download', [StudentController::class, 'downloadStatement'])->name('statements.download');
        
        // Payment processing routes
        Route::post('/payments/initiate', [StudentPaymentController::class, 'initiate'])->name('payments.initiate');
        Route::get('/payments/mpesa/callback', [StudentPaymentController::class, 'mpesaCallback'])->name('payments.mpesa.callback');
        Route::post('/payments/stripe/webhook', [StudentPaymentController::class, 'stripeWebhook'])->name('payments.stripe.webhook');
        Route::get('/payments/success', [StudentPaymentController::class, 'paymentSuccess'])->name('payments.success');
        Route::get('/payments/cancel', [StudentPaymentController::class, 'paymentCancel'])->name('payments.cancel');
        
        // Notifications
        Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [StudentController::class, 'markNotificationRead'])->name('notifications.read');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AdminAuthController::class, 'changePassword'])->name('password.change');
        
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
        });
        
        // Payment Management Routes (requires manage_payments permission)
        Route::middleware('admin.auth:manage_payments')->prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [AdminPaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('verify');
            Route::post('/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
            Route::get('/pending', [AdminPaymentController::class, 'pending'])->name('pending');
            Route::get('/failed', [AdminPaymentController::class, 'failed'])->name('failed');
            Route::post('/bulk-verify', [AdminPaymentController::class, 'bulkVerify'])->name('bulk-verify');
        });
        
        // Fee Management Routes (requires manage_fees permission)
        Route::middleware('admin.auth:manage_fees')->prefix('fees')->name('fees.')->group(function () {
            Route::get('/', [AdminController::class, 'feeStructures'])->name('index');
            Route::get('/create', [AdminController::class, 'createFeeStructure'])->name('create');
            Route::post('/', [AdminController::class, 'storeFeeStructure'])->name('store');
            Route::get('/{feeStructure}/edit', [AdminController::class, 'editFeeStructure'])->name('edit');
            Route::put('/{feeStructure}', [AdminController::class, 'updateFeeStructure'])->name('update');
            Route::delete('/{feeStructure}', [AdminController::class, 'destroyFeeStructure'])->name('destroy');
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
            Route::get('/settings', [AdminAuthController::class, 'systemSettings'])->name('settings');
            Route::get('/admin-management', [AdminAuthController::class, 'adminManagement'])->name('admin-management');
            Route::post('/admin-management', [AdminAuthController::class, 'createAdmin'])->name('admin-management.create');
            Route::put('/admin-management/{admin}', [AdminAuthController::class, 'updateAdminPermissions'])->name('admin-management.update');
        });
    });
});

// Public webhook routes (no authentication required)
Route::post('/webhooks/mpesa', [StudentPaymentController::class, 'mpesaCallback'])->name('webhooks.mpesa');
Route::post('/webhooks/stripe', [StudentPaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');
