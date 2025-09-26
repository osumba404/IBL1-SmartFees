<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display fee structures
     */
    public function feeStructures(Request $request): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = FeeStructure::with(['course', 'semester']);
        
        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $feeStructures = $query->latest()->paginate(15)->withQueryString();
        
        // Get filter options
        $courses = Course::where('status', 'active')->get();
        $semesters = Semester::where('status', 'active')->get();

        return view('admin.fees.index', compact('feeStructures', 'courses', 'semesters', 'admin'));
    }

    /**
     * Show form to create fee structure
     */
    public function createFeeStructure(): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('status', 'active')->get();
        $semesters = Semester::where('status', 'active')->get();

        return view('admin.fees.create', compact('courses', 'semesters', 'admin'));
    }

    /**
     * Store new fee structure
     */
    public function storeFeeStructure(Request $request): RedirectResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'lab_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'activity_fee' => 'nullable|numeric|min:0',
            'technology_fee' => 'nullable|numeric|min:0',
            'student_services_fee' => 'nullable|numeric|min:0',
            'graduation_fee' => 'nullable|numeric|min:0',
            'id_card_fee' => 'nullable|numeric|min:0',
            'medical_insurance_fee' => 'nullable|numeric|min:0',
            'accident_insurance_fee' => 'nullable|numeric|min:0',
            'accommodation_fee' => 'nullable|numeric|min:0',
            'meal_plan_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date|after_or_equal:today',
            'effective_until' => 'nullable|date|after:effective_from',
            'late_payment_penalty_rate' => 'nullable|numeric|min:0|max:100',
            'late_payment_fixed_penalty' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'allows_installments' => 'boolean',
            'max_installments' => 'nullable|integer|min:2|max:12',
            'minimum_deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'minimum_deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,archived',
        ]);

        // Check for existing fee structure
        $existing = FeeStructure::where([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ])->where('status', 'active')->first();

        if ($existing) {
            return back()->withErrors([
                'course_id' => 'An active fee structure already exists for this course and semester.'
            ]);
        }

        $course = Course::findOrFail($request->course_id);
        $semester = Semester::findOrFail($request->semester_id);

        $feeStructure = FeeStructure::create([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'fee_structure_code' => FeeStructure::generateFeeStructureCode($course, $semester),
            'name' => "Fee Structure - {$course->name} ({$semester->name})",
            'tuition_fee' => $request->tuition_fee,
            'registration_fee' => $request->registration_fee ?? 0,
            'library_fee' => $request->library_fee ?? 0,
            'lab_fee' => $request->lab_fee ?? 0,
            'examination_fee' => $request->examination_fee ?? 0,
            'activity_fee' => $request->activity_fee ?? 0,
            'technology_fee' => $request->technology_fee ?? 0,
            'student_services_fee' => $request->student_services_fee ?? 0,
            'graduation_fee' => $request->graduation_fee ?? 0,
            'id_card_fee' => $request->id_card_fee ?? 0,
            'medical_insurance_fee' => $request->medical_insurance_fee ?? 0,
            'accident_insurance_fee' => $request->accident_insurance_fee ?? 0,
            'accommodation_fee' => $request->accommodation_fee ?? 0,
            'meal_plan_fee' => $request->meal_plan_fee ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'effective_from' => $request->effective_from,
            'effective_until' => $request->effective_until,
            'late_payment_penalty_rate' => $request->late_payment_penalty_rate ?? 0,
            'late_payment_fixed_penalty' => $request->late_payment_fixed_penalty ?? 0,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'allows_installments' => $request->boolean('allows_installments'),
            'max_installments' => $request->max_installments,
            'minimum_deposit_percentage' => $request->minimum_deposit_percentage ?? 20,
            'minimum_deposit_amount' => $request->minimum_deposit_amount,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }


        /**
     * Display the specified fee structure.
     */
    public function showFeeStructure(FeeStructure $feeStructure): View
    {
        $admin = Auth::guard('admin')->user();
        $feeStructure->load(['course', 'semester']); // Eager load relationships

        return view('admin.fees.show', compact('feeStructure', 'admin'));
    }

    /**
     * Show form to edit fee structure
     */
    public function editFeeStructure(FeeStructure $feeStructure): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('status', 'active')->get();
        $semesters = Semester::where('status', 'active')->get();

        return view('admin.fees.edit', compact('feeStructure', 'courses', 'semesters', 'admin'));
    }

    /**
     * Update fee structure
     */
    public function updateFeeStructure(Request $request, FeeStructure $feeStructure): RedirectResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'lab_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'activity_fee' => 'nullable|numeric|min:0',
            'technology_fee' => 'nullable|numeric|min:0',
            'student_services_fee' => 'nullable|numeric|min:0',
            'graduation_fee' => 'nullable|numeric|min:0',
            'id_card_fee' => 'nullable|numeric|min:0',
            'medical_insurance_fee' => 'nullable|numeric|min:0',
            'accident_insurance_fee' => 'nullable|numeric|min:0',
            'accommodation_fee' => 'nullable|numeric|min:0',
            'meal_plan_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'late_payment_penalty_rate' => 'nullable|numeric|min:0|max:100',
            'late_payment_fixed_penalty' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'allows_installments' => 'boolean',
            'max_installments' => 'nullable|integer|min:2|max:12',
            'minimum_deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'minimum_deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,archived',
        ]);

        // Check for existing fee structure (excluding current one)
        $existing = FeeStructure::where([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ])->where('status', 'active')
          ->where('id', '!=', $feeStructure->id)
          ->first();

        if ($existing) {
            return back()->withErrors([
                'course_id' => 'Another active fee structure already exists for this course and semester.'
            ]);
        }

        $feeStructure->update([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'tuition_fee' => $request->tuition_fee,
            'registration_fee' => $request->registration_fee ?? 0,
            'library_fee' => $request->library_fee ?? 0,
            'lab_fee' => $request->lab_fee ?? 0,
            'examination_fee' => $request->examination_fee ?? 0,
            'activity_fee' => $request->activity_fee ?? 0,
            'technology_fee' => $request->technology_fee ?? 0,
            'student_services_fee' => $request->student_services_fee ?? 0,
            'graduation_fee' => $request->graduation_fee ?? 0,
            'id_card_fee' => $request->id_card_fee ?? 0,
            'medical_insurance_fee' => $request->medical_insurance_fee ?? 0,
            'accident_insurance_fee' => $request->accident_insurance_fee ?? 0,
            'accommodation_fee' => $request->accommodation_fee ?? 0,
            'meal_plan_fee' => $request->meal_plan_fee ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'effective_from' => $request->effective_from,
            'effective_until' => $request->effective_until,
            'late_payment_penalty_rate' => $request->late_payment_penalty_rate ?? 0,
            'late_payment_fixed_penalty' => $request->late_payment_fixed_penalty ?? 0,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'allows_installments' => $request->boolean('allows_installments'),
            'max_installments' => $request->max_installments,
            'minimum_deposit_percentage' => $request->minimum_deposit_percentage ?? 20,
            'minimum_deposit_amount' => $request->minimum_deposit_amount,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.fee-structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    /**
     * Delete fee structure
     */
    public function destroyFeeStructure(FeeStructure $feeStructure): RedirectResponse
    {
        // Check if fee structure is being used by enrollments
        if ($feeStructure->enrollments()->exists()) {
            return back()->withErrors([
                'delete' => 'Cannot delete fee structure that is being used by student enrollments.'
            ]);
        }

        $feeStructure->delete();

        return redirect()->route('admin.fee-structures.index')
            ->with('success', 'Fee structure deleted successfully.');
    }

    /**
     * Toggle fee structure status
     */
    public function toggleFeeStructureStatus(FeeStructure $feeStructure): RedirectResponse
    {
        $feeStructure->update([
            'status' => !$feeStructure->status
        ]);

        $status = $feeStructure->status ? 'activated' : 'deactivated';
        
        return back()->with('success', "Fee structure {$status} successfully.");
    }

    /**
     * Copy fee structure to new semester
     */
    public function copyFeeStructure(Request $request, FeeStructure $feeStructure): RedirectResponse
    {
        $request->validate([
            'target_semester_id' => 'required|exists:semesters,id',
            'adjust_percentage' => 'nullable|numeric|min:-50|max:100',
        ]);

        // Check if target already has fee structure
        $existing = FeeStructure::where([
            'course_id' => $feeStructure->course_id,
            'semester_id' => $request->target_semester_id,
        ])->where('status', 'active')->first();

        if ($existing) {
            return back()->withErrors([
                'target_semester_id' => 'Target semester already has an active fee structure for this course.'
            ]);
        }

        // Calculate adjustment
        $adjustment = 1 + (($request->adjust_percentage ?? 0) / 100);

        // Create new fee structure
        $newFeeStructure = $feeStructure->replicate();
        $newFeeStructure->semester_id = $request->target_semester_id;
        
        // Apply adjustment to fees
        $newFeeStructure->tuition_fee = round($newFeeStructure->tuition_fee * $adjustment, 2);
        $newFeeStructure->registration_fee = round($newFeeStructure->registration_fee * $adjustment, 2);
        $newFeeStructure->library_fee = round($newFeeStructure->library_fee * $adjustment, 2);
        $newFeeStructure->lab_fee = round($newFeeStructure->lab_fee * $adjustment, 2);
        $newFeeStructure->examination_fee = round($newFeeStructure->examination_fee * $adjustment, 2);
        $newFeeStructure->activity_fee = round($newFeeStructure->activity_fee * $adjustment, 2);
        $newFeeStructure->technology_fee = round($newFeeStructure->technology_fee * $adjustment, 2);
        $newFeeStructure->student_services_fee = round($newFeeStructure->student_services_fee * $adjustment, 2);
        $newFeeStructure->graduation_fee = round($newFeeStructure->graduation_fee * $adjustment, 2);
        $newFeeStructure->id_card_fee = round($newFeeStructure->id_card_fee * $adjustment, 2);
        $newFeeStructure->medical_insurance_fee = round($newFeeStructure->medical_insurance_fee * $adjustment, 2);
        $newFeeStructure->accident_insurance_fee = round($newFeeStructure->accident_insurance_fee * $adjustment, 2);
        $newFeeStructure->accommodation_fee = round($newFeeStructure->accommodation_fee * $adjustment, 2);
        $newFeeStructure->meal_plan_fee = round($newFeeStructure->meal_plan_fee * $adjustment, 2);
        $newFeeStructure->discount_amount = round($newFeeStructure->discount_amount * $adjustment, 2);
        
        // Adjust other fees
        if ($newFeeStructure->other_fees) {
            $adjustedOtherFees = [];
            foreach ($newFeeStructure->other_fees as $fee) {
                $adjustedOtherFees[] = [
                    'name' => $fee['name'],
                    'amount' => round($fee['amount'] * $adjustment, 2)
                ];
            }
            $newFeeStructure->other_fees = $adjustedOtherFees;
        }

        // Recalculate total
        $newFeeStructure->total_amount = $newFeeStructure->tuition_fee + 
                                        $newFeeStructure->registration_fee + 
                                        $newFeeStructure->library_fee + 
                                        $newFeeStructure->lab_fee + 
                                        $newFeeStructure->examination_fee + 
                                        $newFeeStructure->activity_fee + 
                                        $newFeeStructure->technology_fee + 
                                        $newFeeStructure->student_services_fee + 
                                        $newFeeStructure->graduation_fee + 
                                        $newFeeStructure->id_card_fee + 
                                        $newFeeStructure->medical_insurance_fee + 
                                        $newFeeStructure->accident_insurance_fee + 
                                        $newFeeStructure->accommodation_fee + 
                                        $newFeeStructure->meal_plan_fee + 
                                        $newFeeStructure->discount_amount;

        if ($newFeeStructure->other_fees) {
            foreach ($newFeeStructure->other_fees as $fee) {
                $newFeeStructure->total_amount += $fee['amount'];
            }
        }

        $newFeeStructure->save();

        return back()->with('success', 'Fee structure copied successfully to target semester.');
    }

    /**
     * Get fee structure breakdown (AJAX)
     */
    public function getFeeBreakdown(FeeStructure $feeStructure)
    {
        return response()->json([
            'tuition_fee' => $feeStructure->tuition_fee,
            'registration_fee' => $feeStructure->registration_fee,
            'library_fee' => $feeStructure->library_fee,
            'lab_fee' => $feeStructure->lab_fee,
            'examination_fee' => $feeStructure->examination_fee,
            'activity_fee' => $feeStructure->activity_fee,
            'technology_fee' => $feeStructure->technology_fee,
            'student_services_fee' => $feeStructure->student_services_fee,
            'graduation_fee' => $feeStructure->graduation_fee,
            'id_card_fee' => $feeStructure->id_card_fee,
            'medical_insurance_fee' => $feeStructure->medical_insurance_fee,
            'accident_insurance_fee' => $feeStructure->accident_insurance_fee,
            'accommodation_fee' => $feeStructure->accommodation_fee,
            'meal_plan_fee' => $feeStructure->meal_plan_fee,
            'discount_amount' => $feeStructure->discount_amount,
            'total_amount' => $feeStructure->total_amount,
            'effective_from' => $feeStructure->effective_from->toDateString(),
            'effective_until' => $feeStructure->effective_until ? $feeStructure->effective_until->toDateString() : null,
            'allows_installments' => $feeStructure->allows_installments,
            'max_installments' => $feeStructure->max_installments,
        ]);
    }
}
