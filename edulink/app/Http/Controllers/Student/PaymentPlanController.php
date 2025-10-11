<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Models\PaymentInstallment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentPlanController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        $paymentPlans = PaymentPlan::whereHas('enrollment', function($query) use ($student) {
            $query->where('student_id', $student->id);
        })->with(['enrollment.course', 'installments'])->get();

        return view('student.payment-plans.index', compact('paymentPlans', 'student'));
    }

    public function show(PaymentPlan $paymentPlan)
    {
        $student = Auth::guard('student')->user();
        
        if ($paymentPlan->enrollment->student_id !== $student->id) {
            abort(403);
        }

        $paymentPlan->load(['enrollment.course', 'installments' => function($query) {
            $query->orderBy('due_date');
        }]);

        return view('student.payment-plans.show', compact('paymentPlan', 'student'));
    }

    public function create(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = StudentEnrollment::findOrFail($request->enrollment_id);
        
        if ($enrollment->student_id !== $student->id) {
            abort(403);
        }

        return view('student.payment-plans.create', compact('enrollment', 'student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:student_enrollments,id',
            'plan_name' => 'required|string|max:255',
            'total_installments' => 'required|integer|min:2|max:12',
            'installment_dates' => 'required|array',
            'installment_amounts' => 'required|array'
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = StudentEnrollment::findOrFail($request->enrollment_id);
        
        if ($enrollment->student_id !== $student->id) {
            abort(403);
        }

        $totalAmount = array_sum($request->installment_amounts);

        $paymentPlan = PaymentPlan::create([
            'student_enrollment_id' => $enrollment->id,
            'plan_name' => $request->plan_name,
            'total_amount' => $totalAmount,
            'total_installments' => $request->total_installments
        ]);

        // Create installments
        for ($i = 0; $i < $request->total_installments; $i++) {
            PaymentInstallment::create([
                'payment_plan_id' => $paymentPlan->id,
                'installment_number' => $i + 1,
                'amount' => $request->installment_amounts[$i],
                'due_date' => $request->installment_dates[$i]
            ]);
        }

        return redirect()->route('student.payment-plans.show', $paymentPlan)
            ->with('success', 'Payment plan created successfully!');
    }

    public function payInstallment(PaymentInstallment $installment)
    {
        $student = Auth::guard('student')->user();
        
        if ($installment->paymentPlan->enrollment->student_id !== $student->id) {
            abort(403);
        }

        if ($installment->status !== 'pending' && $installment->status !== 'overdue') {
            return redirect()->back()->with('error', 'This installment cannot be paid.');
        }

        // Redirect to payment processing
        return redirect()->route('payment.create', [
            'installment_id' => $installment->id,
            'amount' => $installment->total_amount
        ]);
    }
}