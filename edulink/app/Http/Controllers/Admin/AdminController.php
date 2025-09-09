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
            $query->where('is_active', $request->status === 'active');
        }

        $feeStructures = $query->latest()->paginate(15)->withQueryString();
        
        // Get filter options
        $courses = Course::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();

        return view('admin.fees.index', compact('feeStructures', 'courses', 'semesters', 'admin'));
    }

    /**
     * Show form to create fee structure
     */
    public function createFeeStructure(): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();

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
            'exam_fee' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|array',
            'other_fees.*.name' => 'required|string|max:255',
            'other_fees.*.amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'grace_period_days' => 'nullable|integer|min:0',
            'installment_allowed' => 'boolean',
            'max_installments' => 'nullable|integer|min:2|max:12',
            'is_active' => 'boolean',
        ]);

        // Check for existing fee structure
        $existing = FeeStructure::where([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ])->where('is_active', true)->first();

        if ($existing) {
            return back()->withErrors([
                'course_id' => 'An active fee structure already exists for this course and semester.'
            ]);
        }

        // Calculate total amount
        $totalAmount = $request->tuition_fee + 
                      ($request->registration_fee ?? 0) + 
                      ($request->library_fee ?? 0) + 
                      ($request->lab_fee ?? 0) + 
                      ($request->exam_fee ?? 0);

        // Add other fees
        if ($request->other_fees) {
            foreach ($request->other_fees as $fee) {
                $totalAmount += $fee['amount'];
            }
        }

        $feeStructure = FeeStructure::create([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'tuition_fee' => $request->tuition_fee,
            'registration_fee' => $request->registration_fee ?? 0,
            'library_fee' => $request->library_fee ?? 0,
            'lab_fee' => $request->lab_fee ?? 0,
            'exam_fee' => $request->exam_fee ?? 0,
            'other_fees' => $request->other_fees ?? [],
            'total_amount' => $totalAmount,
            'due_date' => $request->due_date,
            'late_fee_percentage' => $request->late_fee_percentage ?? 0,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'installment_allowed' => $request->boolean('installment_allowed'),
            'max_installments' => $request->max_installments,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure created successfully.');
    }

    /**
     * Show form to edit fee structure
     */
    public function editFeeStructure(FeeStructure $feeStructure): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();

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
            'exam_fee' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|array',
            'other_fees.*.name' => 'required|string|max:255',
            'other_fees.*.amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'grace_period_days' => 'nullable|integer|min:0',
            'installment_allowed' => 'boolean',
            'max_installments' => 'nullable|integer|min:2|max:12',
            'is_active' => 'boolean',
        ]);

        // Check for existing fee structure (excluding current one)
        $existing = FeeStructure::where([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ])->where('is_active', true)
          ->where('id', '!=', $feeStructure->id)
          ->first();

        if ($existing) {
            return back()->withErrors([
                'course_id' => 'Another active fee structure already exists for this course and semester.'
            ]);
        }

        // Calculate total amount
        $totalAmount = $request->tuition_fee + 
                      ($request->registration_fee ?? 0) + 
                      ($request->library_fee ?? 0) + 
                      ($request->lab_fee ?? 0) + 
                      ($request->exam_fee ?? 0);

        // Add other fees
        if ($request->other_fees) {
            foreach ($request->other_fees as $fee) {
                $totalAmount += $fee['amount'];
            }
        }

        $feeStructure->update([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'tuition_fee' => $request->tuition_fee,
            'registration_fee' => $request->registration_fee ?? 0,
            'library_fee' => $request->library_fee ?? 0,
            'lab_fee' => $request->lab_fee ?? 0,
            'exam_fee' => $request->exam_fee ?? 0,
            'other_fees' => $request->other_fees ?? [],
            'total_amount' => $totalAmount,
            'due_date' => $request->due_date,
            'late_fee_percentage' => $request->late_fee_percentage ?? 0,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'installment_allowed' => $request->boolean('installment_allowed'),
            'max_installments' => $request->max_installments,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.fees.index')
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

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure deleted successfully.');
    }

    /**
     * Toggle fee structure status
     */
    public function toggleFeeStructureStatus(FeeStructure $feeStructure): RedirectResponse
    {
        $feeStructure->update([
            'is_active' => !$feeStructure->is_active
        ]);

        $status = $feeStructure->is_active ? 'activated' : 'deactivated';
        
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
        ])->where('is_active', true)->first();

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
        $newFeeStructure->exam_fee = round($newFeeStructure->exam_fee * $adjustment, 2);
        
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
                                        $newFeeStructure->exam_fee;

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
            'exam_fee' => $feeStructure->exam_fee,
            'other_fees' => $feeStructure->other_fees,
            'total_amount' => $feeStructure->total_amount,
            'due_date' => $feeStructure->due_date->toDateString(),
            'installment_allowed' => $feeStructure->installment_allowed,
            'max_installments' => $feeStructure->max_installments,
        ]);
    }
}
