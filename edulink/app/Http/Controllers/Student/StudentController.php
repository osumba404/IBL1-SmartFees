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

use App\Models\Semester;

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

            $currentSemester = Semester::current();

            return view('student.courses', compact('availableCourses', 'enrolledCourses', 'student', 'currentSemester'));
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
     * Display enrollment form
     */
    public function enroll(): View
    {
        $student = Auth::guard('student')->user();
        
        // Get all available courses for enrollment
        $now = now();
        $availableCourses = Course::where('status', 'active')
            ->where(function($query) use ($now) {
                $query->whereNull('available_from')
                      ->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('available_until')
                      ->orWhere('available_until', '>=', $now);
            })
            ->get();

                // Get the current active semester (auto-select)
                $currentSemester = Semester::current() ?? Semester::where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->first();

        // Get student's already enrolled courses to prevent duplicate enrollments
        $enrolledCourses = $student->enrollments()
            ->where('semester_id', $currentSemester?->id)
            ->pluck('course_id')
            ->toArray();

        // Filter out courses the student is already enrolled in for this semester
        $availableCourses = $availableCourses->whereNotIn('id', $enrolledCourses);

        return view('student.enroll', compact('availableCourses', 'currentSemester', 'student'));
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
            // Use course total_fee if enrollment total_fees_due is 0
            $totalFees = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 50000);
            $paidAmount = $enrollment->fees_paid;
            $outstanding = $totalFees - $paidAmount;
            
            $feeSummary['total_fees'] += $totalFees;
            $feeSummary['total_paid'] += $paidAmount;
            $feeSummary['total_pending'] += max(0, $outstanding);
            
            // Check for overdue amounts
            if ($enrollment->next_payment_due && $enrollment->next_payment_due->isPast() && $outstanding > 0) {
                $feeSummary['total_overdue'] += $outstanding;
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

        $payment->load(['enrollment.course', 'enrollment.semester']);

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
    public function downloadStatement(Request $request)
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

        // Return HTML statement view instead of PDF
        return view('student.statements.pdf', compact('student', 'enrollment'));
    }

    /**
     * Download fee statement as PDF
     */
    public function downloadStatementPDF(Request $request)
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

        $html = view('student.statements.pdf', compact('student', 'enrollment'))->render();
        
        $filename = 'fee-statement-' . $student->student_id . '-' . date('Y-m-d') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
 * Handle enrollment deferment request
 */
public function deferEnrollment(Request $request, StudentEnrollment $enrollment)
{
    // Ensure the enrollment belongs to the authenticated student
    if ($enrollment->student_id !== auth('student')->id()) {
        abort(403, 'Unauthorized action.');
    }

    $validated = $request->validate([
        'reason' => 'required|string|max:500',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
    ]);

    try {
        // Update enrollment status
        $enrollment->update([
            'status' => 'pending_deferment',
            'status_change_reason' => $validated['reason'],
            'is_deferred' => true,
            'deferment_start_date' => $validated['start_date'],
            'deferment_end_date' => $validated['end_date'],
            'deferment_reason' => $validated['reason'],
        ]);

        // Notify admin about the deferment request
        // You'll need to implement your notification system here
        // Example: Notification::send(/* admin users */, new EnrollmentDefermentRequested($enrollment));

        return redirect()->back()->with('success', 'Your deferment request has been submitted for approval.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to process deferment request. Please try again.');
    }
}




    /**
     * Handle enrollment resumption request
     */
    public function resumeEnrollment(StudentEnrollment $enrollment)
    {
        // Ensure the enrollment belongs to the authenticated student
        if ($enrollment->student_id !== auth('student')->id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Update enrollment status
            $enrollment->update([
                'status' => 'active',
                'status_change_reason' => 'Student resumed studies after deferment',
                'is_deferred' => false,
                'deferment_end_date' => now(),
            ]);

            return redirect()->back()->with('success', 'Your enrollment has been resumed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to resume enrollment. Please try again.');
        }
    }



    

    /**
 * Get fee details for a specific enrollment (AJAX).
 */
public function getFeeDetails(StudentEnrollment $enrollment)
{
    // Ensure the authenticated student owns this enrollment
    if ($enrollment->student_id !== auth('student')->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $feeStructure = $enrollment->feeStructure;

    if (!$feeStructure) {
        return response()->json(['success' => false, 'message' => 'No fee structure found for this enrollment.'], 404);
    }

    // Return a subset of fee details suitable for students
    return response()->json([
        'success' => true,
        'course_name' => $enrollment->course->name,
        'semester_name' => $enrollment->semester->name,
        'fee_structure_code' => $feeStructure->fee_structure_code,
        'total_amount' => number_format($feeStructure->total_amount, 2),
        'breakdown' => [
            'Tuition Fee' => number_format($feeStructure->tuition_fee, 2),
            'Registration Fee' => number_format($feeStructure->registration_fee, 2),
            'Library Fee' => number_format($feeStructure->library_fee, 2),
            'Lab Fee' => number_format($feeStructure->lab_fee, 2),
            'Examination Fee' => number_format($feeStructure->examination_fee, 2),
            'Activity Fee' => number_format($feeStructure->activity_fee, 2),
            'Technology Fee' => number_format($feeStructure->technology_fee, 2),
            'Student Services Fee' => number_format($feeStructure->student_services_fee, 2),
        ],
        'enrollment_status' => ucfirst($enrollment->status),
        'payment_plan' => ucwords(str_replace('_', ' ', $enrollment->payment_plan)),
        'total_paid' => number_format($enrollment->fees_paid, 2),
        'outstanding_balance' => number_format($enrollment->outstanding_balance, 2),
        'next_payment_due' => $enrollment->next_payment_due ? $enrollment->next_payment_due->format('F j, Y') : 'N/A',
    ]);
}



        /**
     * Display the specified enrollment.
     */
    public function showEnrollment(StudentEnrollment $enrollment): View
    {
        // Ensure the enrollment belongs to the authenticated student
        if ($enrollment->student_id !== auth('student')->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load related data
        $enrollment->load(['course', 'semester', 'feeStructure', 'payments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('student.enrollments.show', compact('enrollment'));
    }

    /**
     * Store a new enrollment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'enrollment_type' => 'required|in:new,continuing,transfer,readmission',
            'payment_plan' => 'required|in:full_payment,installments',
        ]);

        $student = Auth::guard('student')->user();
        $course = Course::findOrFail($request->course_id);
        
        // Determine current semester and validate registration window
        $semester = Semester::current() ?? Semester::where('status', 'active')
        ->orderBy('start_date', 'desc')
        ->first();

        // If no active semester, try to find any semester or create a default one
        if (!$semester) {
            $semester = Semester::orderBy('created_at', 'desc')->first();
            
            if (!$semester) {
                // Create a default semester if none exists
                $semester = Semester::create([
                    'semester_code' => 'SEM' . date('Y') . '01',
                    'name' => 'Semester 1',
                    'academic_year' => date('Y'),
                    'period' => 'semester_1',
                    'start_date' => now(),
                    'end_date' => now()->addMonths(6),
                    'registration_start_date' => now(),
                    'registration_end_date' => now()->addMonths(1),
                    'fee_payment_deadline' => now()->addMonths(1),
                    'status' => 'active',
                    'is_current_semester' => true,
                ]);
            }
        }

        // Check if student is already enrolled in this course and semester
        $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->where('semester_id', $semester->id)
            ->first();

        if ($existingEnrollment) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this course for the current semester.'
                ], 422);
            }
            return redirect()->route('student.enrollment.error')
                ->with('error_message', 'You are already enrolled in this course for the current semester.');
        }

        // Validate course availability and capacity
        if (!$course->isAvailable()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is not currently available for enrollment.'
                ], 422);
            }
            return redirect()->route('student.enrollment.error')
                ->with('error_message', 'This course is not currently available for enrollment.');
        }


        // Allow registration if semester is active (remove strict date checks for now)
        if ($semester->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration is currently closed for the active semester.'
                ], 422);
            }
            return redirect()->route('student.enrollment.error')
                ->with('error_message', 'Registration is currently closed for the active semester.');
        }

            // Check course capacity
            if ($course->max_students !== null) {
                $currentEnrollments = StudentEnrollment::where('course_id', $course->id)
                    ->where('semester_id', $semester->id)
                    ->where('status', 'enrolled')
                    ->count();

            if ($currentEnrollments >= $course->max_students) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This course has reached its maximum enrollment capacity for the current semester.'
                    ], 422);
                }
                return redirect()->route('student.enrollment.error')
                    ->with('error_message', 'This course has reached its maximum enrollment capacity for the current semester.');
            }
        }

        // Calculate total fees using course total_fee or sum of individual fees
        $totalFees = $course->total_fee ?? (
            ($course->tuition_fee ?? 0) + 
            ($course->registration_fee ?? 0) + 
            ($course->examination_fee ?? 0) + 
            ($course->library_fee ?? 0) + 
            ($course->lab_fee ?? 0)
        );

        // Calculate installment details if payment plan is installments
        $installmentCount = null;
        $installmentAmount = null;
        $nextPaymentDue = now()->addDays(30);
        
        if ($request->payment_plan === 'installments') {
            // Ensure installment count is at least 1, defaulting to 4 if not set.
            $installmentCount = $course->max_installments > 0 ? min($course->max_installments, 12) : 4;
            $processingFee = $totalFees * 0.02; // 2% processing fee for installments
            $totalWithFee = $totalFees + $processingFee;
            $installmentAmount = $totalWithFee / $installmentCount;
        } else {
            // Apply 5% discount for full payment
            $totalFees = $totalFees * 0.95;
        }

        try {
            \DB::beginTransaction();

            $now = now();
            // Create enrollment record. Status must be valid per migration enum.
            $enrollment = StudentEnrollment::create([
                'enrollment_number' => StudentEnrollment::generateEnrollmentNumber(),
                'student_id' => $student->id,
                'course_id' => $course->id,
                'semester_id' => $semester->id,
                'enrollment_date' => $now,
                'enrollment_type' => $request->enrollment_type,
                'status' => 'enrolled',
                'payment_plan' => $request->payment_plan,
                'total_fees_due' => $totalFees,
                'fees_paid' => 0,
                'outstanding_balance' => $totalFees,
                'fees_fully_paid' => false,
                'installment_count' => $installmentCount,
                'installment_amount' => $installmentAmount,
                'next_payment_due' => $request->payment_plan === 'installments' ? $nextPaymentDue : $semester->fee_payment_deadline,
            ]);

            $payment = null;
            // Create initial payment record if first installment is due
            if ($request->payment_plan === 'installments' && $installmentAmount) {
                $payment = new \App\Models\Payment([
                    'student_id' => $student->id,
                    'student_enrollment_id' => $enrollment->id,
                    'amount' => $installmentAmount,
                    'payment_method' => 'pending',
                    'status' => 'pending',
                    'due_date' => $nextPaymentDue,
                    'description' => 'Initial installment payment for ' . $course->name,
                ]);
                $payment->save();
            }

            // Create notification for student
            PaymentNotification::create([
                'student_id' => $student->id,
                'payment_id' => $payment->id ?? null,
                'title' => 'Enrollment Submitted',
                'message' => "Your enrollment for {$course->name} has been submitted. Please complete your payment to secure your spot.",
                'notification_type' => 'enrollment', 
            ]);

            \DB::commit();

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment submitted successfully! Please complete your payment to secure your spot.',
                    'enrollment_number' => $enrollment->enrollment_number,
                ]);
            }
            
            // Redirect to success page for regular form submissions
            return redirect()->route('student.enrollment.success', $enrollment->id)
                ->with('success', 'Enrollment submitted successfully! Please complete your payment to secure your spot.');

        } catch (\Exception $e) {
            \DB::rollBack();
            // Log the detailed error for the developer
            \Log::error('Enrollment Error - Student ID: ' . ($student->id ?? 'N/A') . ' - ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Prepare a detailed error message for debug mode
            $errorMessage = 'An error occurred while processing your enrollment. Please try again or contact support if the problem persists.';
            if (config('app.debug')) {
                $errorMessage = 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->route('student.enrollment.error')
                ->with('error_message', $errorMessage);
        }
    }
    
    /**
     * Display enrollment success page
     */
    public function enrollmentSuccess(StudentEnrollment $enrollment): View
    {
        $student = Auth::guard('student')->user();
        
        // Ensure enrollment belongs to the authenticated student
        if ($enrollment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to enrollment.');
        }
        
        $enrollment->load(['course', 'semester']);
        
        return view('student.enrollment-success', compact('enrollment', 'student'));
    }
    
    /**
     * Display enrollment error page
     */
    public function enrollmentError(): View
    {
        $student = Auth::guard('student')->user();
        $error_message = session('error_message', 'An unexpected error occurred during enrollment.');
        
        return view('student.enrollment-error', compact('student', 'error_message'));
    }
}
