<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use App\Models\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Display student courses
     */
    public function courses(): View
    {
        $student = Auth::guard('student')->user();
        
        // Get all available courses
        $availableCourses = Course::where('status', 'active')
            ->get();

        // Get student's enrolled courses
        $enrolledCourses = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure'])
            ->get()
            ->pluck('course_id')
            ->toArray();

        return view('student.courses', compact('availableCourses', 'enrolledCourses', 'student'));
    }

    /**
     * Display student enrollments
     */
    public function enrollments(): View
    {
        $student = Auth::guard('student')->user();
        
        $enrollments = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure', 'payments'])
            ->latest()
            ->paginate(10);

        return view('student.enrollments', compact('enrollments', 'student'));
    }

    /**
     * Display student fees
     */
    public function fees(): View
    {
        $student = Auth::guard('student')->user();
        
        // Get current active enrollment
        $currentEnrollment = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure'])
            ->where('status', 'active')
            ->first();

        // Get all enrollments with fee details
        $enrollments = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure', 'payments'])
            ->get();

        // Calculate fee summary
        $feeSummary = [
            'total_fees' => 0,
            'total_paid' => 0,
            'total_pending' => 0,
            'total_overdue' => 0,
        ];

        foreach ($enrollments as $enrollment) {
            if ($enrollment->feeStructure) {
                $feeSummary['total_fees'] += $enrollment->feeStructure->total_amount;
                $feeSummary['total_paid'] += $enrollment->amount_paid;
                $feeSummary['total_pending'] += $enrollment->amount_pending;
                
                // Check for overdue amounts
                if ($enrollment->feeStructure->due_date && $enrollment->feeStructure->due_date->isPast() && $enrollment->amount_pending > 0) {
                    $feeSummary['total_overdue'] += $enrollment->amount_pending;
                }
            }
        }

        return view('student.fees', compact('currentEnrollment', 'enrollments', 'feeSummary', 'student'));
    }

    /**
     * Display student payments
     */
    public function payments(): View
    {
        $student = Auth::guard('student')->user();
        
        $payments = $student->payments()
            ->with(['enrollment.course', 'enrollment.semester'])
            ->latest()
            ->paginate(15);

        // Payment statistics
        $paymentStats = [
            'total_payments' => $student->payments()->where('status', 'completed')->sum('amount'),
            'pending_payments' => $student->payments()->where('status', 'pending')->sum('amount'),
            'failed_payments' => $student->payments()->where('status', 'failed')->count(),
            'last_payment' => $student->payments()->where('status', 'completed')->latest()->first(),
        ];

        return view('student.payments', compact('payments', 'paymentStats', 'student'));
    }

    /**
     * Display payment details
     */
    public function paymentDetails(Payment $payment): View
    {
        $student = Auth::guard('student')->user();
        
        // Ensure payment belongs to the authenticated student
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment details.');
        }

        $payment->load(['enrollment.course', 'enrollment.semester', 'notifications']);

        return view('student.payment-details', compact('payment', 'student'));
    }

    /**
     * Display student statements
     */
    public function statements(): View
    {
        $student = Auth::guard('student')->user();
        
        // Get enrollments with payments for statements
        $enrollments = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure', 'payments' => function($query) {
                $query->where('status', 'completed')->latest();
            }])
            ->get();

        // Get available statement periods (semesters with payments)
        $statementPeriods = $enrollments->filter(function($enrollment) {
            return $enrollment->payments->count() > 0;
        });

        return view('student.statements', compact('enrollments', 'statementPeriods', 'student'));
    }

    /**
     * Download fee statement
     */
    public function downloadStatement(Request $request): Response
    {
        $student = Auth::guard('student')->user();
        
        $request->validate([
            'enrollment_id' => 'required|exists:student_enrollments,id',
        ]);

        $enrollment = StudentEnrollment::with(['course', 'semester', 'feeStructure', 'payments' => function($query) {
            $query->where('status', 'completed')->latest();
        }])->findOrFail($request->enrollment_id);

        // Ensure enrollment belongs to the authenticated student
        if ($enrollment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to statement.');
        }

        // Generate PDF statement
        $pdf = Pdf::loadView('student.statement-pdf', compact('student', 'enrollment'));
        
        $filename = sprintf(
            'fee-statement-%s-%s-%s.pdf',
            $student->student_id,
            $enrollment->course->code,
            $enrollment->semester->code
        );

        return $pdf->download($filename);
    }

    /**
     * Display student notifications
     */
    public function notifications(): View
    {
        $student = Auth::guard('student')->user();
        
        $notifications = PaymentNotification::where('student_id', $student->id)
            ->with(['payment.enrollment.course'])
            ->latest()
            ->paginate(20);

        // Mark notifications as read when viewed
        PaymentNotification::where('student_id', $student->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('student.notifications', compact('notifications', 'student'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(PaymentNotification $notification)
    {
        $student = Auth::guard('student')->user();
        
        // Ensure notification belongs to the authenticated student
        if ($notification->student_id !== $student->id) {
            abort(403, 'Unauthorized access to notification.');
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get student dashboard data (AJAX)
     */
    public function dashboardData(): array
    {
        $student = Auth::guard('student')->user();
        
        return [
            'financial_summary' => $student->getFinancialSummary(),
            'recent_payments' => $student->payments()
                ->with(['enrollment.course'])
                ->latest()
                ->take(5)
                ->get(),
            'pending_payments' => $student->payments()
                ->where('status', 'pending')
                ->with(['enrollment.course'])
                ->get(),
            'unread_notifications' => PaymentNotification::where('student_id', $student->id)
                ->whereNull('read_at')
                ->count(),
        ];
    }

    /**
     * Search student payments
     */
    public function searchPayments(Request $request): View
    {
        $student = Auth::guard('student')->user();
        
        $query = $student->payments()->with(['enrollment.course', 'enrollment.semester']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('student.payments', compact('payments', 'student'));
    }

    /**
     * Display student payment history
     */
    public function paymentHistory(): View
    {
        $student = Auth::guard('student')->user();
        
        $payments = $student->payments()
            ->with(['enrollment.course', 'enrollment.semester'])
            ->latest()
            ->paginate(20);

        // Payment statistics
        $paymentStats = [
            'total_payments' => $student->payments()->where('status', 'completed')->sum('amount'),
            'pending_payments' => $student->payments()->where('status', 'pending')->sum('amount'),
            'failed_payments' => $student->payments()->where('status', 'failed')->count(),
            'last_payment' => $student->payments()->where('status', 'completed')->latest()->first(),
        ];

        return view('student.payment-history', compact('payments', 'paymentStats', 'student'));
    }

    /**
     * Get payment methods available to student
     */
    public function getPaymentMethods(): array
    {
        $paymentMethods = config('services.payment_methods', []);
        
        // Filter enabled payment methods
        $enabledMethods = array_filter($paymentMethods, function($method) {
            return $method['enabled'] ?? false;
        });

        return $enabledMethods;
    }

    /**
     * Store enrollment request
     */
    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'enrollment_type' => 'required|in:new,continuing,transfer,readmission',
            'payment_plan' => 'required|in:full_payment,installments',
        ]);

        $student = Auth::guard('student')->user();
        $course = Course::findOrFail($request->course_id);

        // Check if student is already enrolled in this course
        $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Check if course has available capacity
        $currentEnrollments = StudentEnrollment::where('course_id', $course->id)
            ->where('status', '!=', 'withdrawn')
            ->count();

        if ($course->enrollment_capacity && $currentEnrollments >= $course->enrollment_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'This course has reached its enrollment capacity.'
            ]);
        }

        // Create enrollment record
        $enrollment = StudentEnrollment::create([
            'enrollment_number' => StudentEnrollment::generateEnrollmentNumber(),
            'student_id' => $student->id,
            'course_id' => $course->id,
            'semester_id' => $request->semester_id,
            'enrollment_date' => now(),
            'enrollment_type' => $request->enrollment_type,
            'status' => 'enrolled',
            'payment_plan' => $request->payment_plan,
            'total_fees_due' => $course->total_fee,
            'fees_paid' => 0,
            'outstanding_balance' => $course->total_fee,
            'fees_fully_paid' => false,
            'installment_count' => $request->payment_plan === 'installments' ? 4 : null,
            'installment_amount' => $request->payment_plan === 'installments' ? ($course->total_fee / 4) : null,
            'next_payment_due' => now()->addDays(30), // First payment due in 30 days
        ]);

        // Create notification for student
        PaymentNotification::create([
            'student_id' => $student->id,
            'title' => 'Enrollment Successful',
            'message' => "You have been successfully enrolled in {$course->name}. Please proceed with fee payment.",
            'type' => 'enrollment',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment successful! Please proceed with fee payment.',
            'enrollment_number' => $enrollment->enrollment_number
        ]);
    }
}
