<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Semester;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Payment::with(['student', 'enrollment.course', 'enrollment.semester']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('payment_reference', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        
        if ($request->filled('semester_id')) {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
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

        $payments = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $courses = Course::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $statuses = ['pending', 'completed', 'failed', 'cancelled', 'refunded', 'pending_verification'];
        $paymentMethods = ['mpesa', 'stripe', 'bank_transfer', 'cash'];

        // Payment statistics
        $todayPayments = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');
        $completedCount = Payment::where('status', 'completed')->count();
        $pendingCount = Payment::where('status', 'pending')->count();
        $failedCount = Payment::where('status', 'failed')->count();

        return view('admin.payments.index', compact(
            'payments', 'courses', 'semesters', 'statuses', 
            'paymentMethods', 'todayPayments', 'completedCount', 
            'pendingCount', 'failedCount', 'admin'
        ));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment): View
    {
        $admin = Auth::guard('admin')->user();
        
        $payment->load([
            'student',
            'enrollment.course',
            'enrollment.semester',
            'enrollment.feeStructure'
        ]);

        return view('admin.payments.show', compact('payment', 'admin'));
    }

    /**
     * Verify a payment
     */
    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to verify payments.');
        }

        $request->validate([
            'verification_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $result = $this->paymentService->verifyPayment($payment->id, [
                'verified_by' => $admin->id,
                'verification_notes' => $request->verification_notes,
            ]);

            if ($result['success']) {
                DB::commit();
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $result['message']]);
                }
                return back()->with('success', 'Payment verified successfully.');
            } else {
                DB::rollBack();
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $result['message']]);
                }
                return back()->withErrors(['verify' => $result['message']]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Payment verification failed. Please try again.']);
            }
            return back()->withErrors(['verify' => 'Payment verification failed. Please try again.']);
        }
    }

    /**
     * Refund a payment
     */
    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to process refunds.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $result = $this->paymentService->refundPayment($payment->id, [
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->refund_reason,
                'refunded_by' => $admin->id,
            ]);

            if ($result['success']) {
                DB::commit();
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $result['message']]);
                }
                return back()->with('success', 'Payment refunded successfully.');
            } else {
                DB::rollBack();
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $result['message']]);
                }
                return back()->withErrors(['refund' => $result['message']]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Payment refund failed. Please try again.']);
            }
            return back()->withErrors(['refund' => 'Payment refund failed. Please try again.']);
        }
    }

    /**
     * Display pending payments
     */
    public function pending(): View
    {
        $admin = Auth::guard('admin')->user();
        
        $payments = Payment::with(['student', 'enrollment.course', 'enrollment.semester'])
            ->whereIn('status', ['pending', 'pending_verification'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.pending', compact('payments', 'admin'));
    }

    /**
     * Display failed payments
     */
    public function failed(): View
    {
        $admin = Auth::guard('admin')->user();
        
        $payments = Payment::with(['student', 'enrollment.course', 'enrollment.semester'])
            ->where('status', 'failed')
            ->latest()
            ->paginate(20);

        return view('admin.payments.failed', compact('payments', 'admin'));
    }

    /**
     * Bulk verify payments
     */
    public function bulkVerify(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to verify payments.');
        }

        $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'exists:payments,id',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $payments = Payment::whereIn('id', $request->payment_ids)
                ->where('status', 'pending_verification')
                ->get();

            $verifiedCount = 0;
            foreach ($payments as $payment) {
                $result = $this->paymentService->verifyPayment($payment->id, [
                    'verified_by' => $admin->id,
                    'verification_notes' => $request->verification_notes,
                ]);

                if ($result['success']) {
                    $verifiedCount++;
                }
            }

            DB::commit();
            
            return back()->with('success', "Successfully verified {$verifiedCount} payments.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['bulk_verify' => 'Bulk verification failed. Please try again.']);
        }
    }

    /**
     * Bulk update payment status
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to update payments.');
        }

        $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'exists:payments,id',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $count = Payment::whereIn('id', $request->payment_ids)
            ->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_at' => now(),
            ]);

        return back()->with('success', "Successfully updated {$count} payments.");
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_view_reports && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to export payments.');
        }

        $query = Payment::with(['student', 'enrollment.course', 'enrollment.semester']);
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        $filename = 'payments-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Payment Reference', 'Student ID', 'Student Name', 'Course', 
                'Semester', 'Amount', 'Payment Method', 'Status', 
                'Payment Date', 'Processed Date', 'Verified'
            ]);

            // CSV data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_reference,
                    $payment->student->student_id,
                    $payment->student->getFullNameAttribute(),
                    $payment->enrollment->course->name ?? 'N/A',
                    $payment->enrollment->semester->name ?? 'N/A',
                    $payment->amount,
                    $payment->payment_method,
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->processed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $payment->is_verified ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get payment statistics (AJAX)
     */
    public function getStats(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $period = $request->get('period', 'today'); // today, week, month, year
        
        $query = Payment::where('status', 'completed');
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $stats = [
            'total_amount' => $query->sum('amount'),
            'total_count' => $query->count(),
            'average_amount' => $query->avg('amount'),
            'by_method' => Payment::where('status', 'completed')
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Retry failed payment
     */
    public function retry(Payment $payment): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to retry payments.');
        }

        if ($payment->status !== 'failed') {
            return back()->withErrors(['retry' => 'Only failed payments can be retried.']);
        }

        try {
            $result = $this->paymentService->retryPayment($payment->id);

            if ($result['success']) {
                return back()->with('success', 'Payment retry initiated successfully.');
            } else {
                return back()->withErrors(['retry' => $result['message']]);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['retry' => 'Payment retry failed. Please try again.']);
        }
    }

    /**
     * Manual payment entry
     */
    public function manualEntry(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to create manual payments.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'enrollment_id' => 'required|exists:student_enrollments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $payment = $this->paymentService->createPayment([
                'student_id' => $request->student_id,
                'enrollment_id' => $request->enrollment_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
                'status' => 'completed',
                'processed_at' => now(),
                'is_verified' => true,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            // Update enrollment payment status
            $payment->enrollment->updatePaymentStatus($payment->amount);

            DB::commit();
            
            return back()->with('success', 'Manual payment entry created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['manual' => 'Manual payment entry failed. Please try again.']);
        }
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to create payments.');
        }

        $students = Student::active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        $courses = Course::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();
            
        $paymentMethods = [
            'mpesa' => 'M-Pesa',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'other' => 'Other'
        ];

        return view('admin.payments.create', compact('students', 'courses', 'semesters', 'paymentMethods', 'admin'));
    }

    /**
     * Store a newly created payment in storage
     */
    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_manage_payments && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to create payments.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:mpesa,bank_transfer,cash,cheque,other',
            'payment_reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create the payment record
            $payment = new Payment();
            $payment->student_id = $validated['student_id'];
            $payment->enrollment_id = null; // Will be set after enrollment is created/retrieved
            $payment->amount = $validated['amount'];
            $payment->payment_date = $validated['payment_date'];
            $payment->payment_method = $validated['payment_method'];
            $payment->payment_reference = $validated['payment_reference'] ?? 'MANUAL-' . strtoupper(uniqid());
            $payment->description = $validated['description'] ?? 'Manual payment';
            $payment->status = $validated['status'];
            $payment->notes = $validated['notes'] ?? null;
            $payment->verified_by = $admin->id;
            $payment->verified_at = now();
            $payment->save();

            // Create activity log
            activity()
                ->causedBy($admin)
                ->performedOn($payment)
                ->withProperties([
                    'amount' => $payment->amount,
                    'method' => $payment->payment_method,
                    'status' => $payment->status
                ])
                ->log('Payment manually recorded');

            // If payment is marked as completed, process it
            if ($payment->status === 'completed') {
                $this->paymentService->processManualPayment($payment->id);
            }

            DB::commit();

            return redirect()
                ->route('admin.payments.show', $payment->id)
                ->with('success', 'Payment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating payment: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record payment. Please try again.']);
        }
    }
}
