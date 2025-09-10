<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Semester::withCount(['enrollments', 'feeStructures']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('semester_code', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        if ($request->filled('semester_type')) {
            $query->where('semester_type', $request->semester_type);
        }

        $semesters = $query->latest('start_date')->paginate(15)->withQueryString();
        
        // Get filter options
        $academicYears = Semester::distinct()->pluck('academic_year')->filter()->sort();
        $semesterTypes = ['fall', 'spring', 'summer', 'winter'];

        return view('admin.semesters.index', compact('semesters', 'academicYears', 'semesterTypes', 'admin'));
    }

    /**
     * Show the form for creating a new semester
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.semesters.create', compact('admin'));
    }

    /**
     * Store a newly created semester
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester_code' => 'required|string|max:20|unique:semesters,semester_code',
            'academic_year' => 'required|string|max:20',
            'semester_type' => 'required|in:fall,spring,summer,winter',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date|before_or_equal:start_date',
            'registration_end' => 'required|date|after:registration_start|before_or_equal:start_date',
            'fee_due_date' => 'required|date|after_or_equal:registration_start',
            'late_fee_start_date' => 'nullable|date|after:fee_due_date',
            'grace_period_days' => 'nullable|integer|min:0|max:30',
            'max_credits' => 'nullable|integer|min:1',
            'min_credits' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate credit limits
        if ($request->min_credits && $request->max_credits && $request->min_credits > $request->max_credits) {
            return back()->withErrors([
                'min_credits' => 'Minimum credits cannot be greater than maximum credits.'
            ]);
        }

        // Map semester_type to period enum
        $periodMap = [
            'fall' => 'semester_1',
            'spring' => 'semester_2', 
            'summer' => 'summer',
            'winter' => 'winter'
        ];

        $semester = Semester::create([
            'name' => $request->name,
            'semester_code' => strtoupper($request->semester_code),
            'academic_year' => (int) explode('-', $request->academic_year)[0], // Extract first year
            'period' => $periodMap[$request->semester_type],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'registration_start_date' => $request->registration_start,
            'registration_end_date' => $request->registration_end,
            'fee_payment_deadline' => $request->fee_due_date,
            'late_payment_deadline' => $request->late_fee_start_date,
            'grace_period_days' => $request->grace_period_days ?? 7,
            'max_credits_per_student' => $request->max_credits,
            'min_credits_per_student' => $request->min_credits,
            'status' => $request->status === 'active' ? 'active' : 'upcoming',
        ]);

        return redirect()->route('admin.semesters.show', $semester)
            ->with('success', 'Semester created successfully.');
    }

    /**
     * Display the specified semester
     */
    public function show(Semester $semester): View
    {
        $admin = Auth::guard('admin')->user();
        
        // Get semester statistics
        $stats = [
            'total_enrollments' => $semester->enrollments()->count(),
            'active_enrollments' => $semester->enrollments()->where('status', 'active')->count(),
            'completed_enrollments' => $semester->enrollments()->where('status', 'completed')->count(),
            'pending_enrollments' => $semester->enrollments()->where('status', 'pending')->count(),
            'total_students' => $semester->enrollments()->distinct('student_id')->count(),
            'total_revenue' => $semester->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->sum('payments.amount'),
        ];

        // Get recent enrollments
        $recentEnrollments = $semester->enrollments()
            ->with(['student', 'course'])
            ->latest()
            ->take(10)
            ->get();

        // Get fee structures
        $feeStructures = $semester->feeStructures()
            ->with('course')
            ->latest()
            ->take(10)
            ->get();

        // Get enrollment statistics by course
        $enrollmentsByCourse = $semester->enrollments()
            ->with('course')
            ->selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return view('admin.semesters.show', compact(
            'semester', 'stats', 'recentEnrollments', 
            'feeStructures', 'enrollmentsByCourse', 'admin'
        ));
    }

    /**
     * Show the form for editing the specified semester
     */
    public function edit(Semester $semester): View
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.semesters.edit', compact('semester', 'admin'));
    }

    /**
     * Update the specified semester
     */
    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester_code' => 'required|string|max:20|unique:semesters,semester_code,' . $semester->id,
            'academic_year' => 'required|string|max:20',
            'semester_type' => 'required|in:fall,spring,summer,winter',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date|before_or_equal:start_date',
            'registration_end' => 'required|date|after:registration_start|before_or_equal:start_date',
            'fee_due_date' => 'required|date|after_or_equal:registration_start',
            'late_fee_start_date' => 'nullable|date|after:fee_due_date',
            'grace_period_days' => 'nullable|integer|min:0|max:30',
            'max_credits' => 'nullable|integer|min:1',
            'min_credits' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate credit limits
        if ($request->min_credits && $request->max_credits && $request->min_credits > $request->max_credits) {
            return back()->withErrors([
                'min_credits' => 'Minimum credits cannot be greater than maximum credits.'
            ]);
        }

        // Map semester_type to period enum
        $periodMap = [
            'fall' => 'semester_1',
            'spring' => 'semester_2', 
            'summer' => 'summer',
            'winter' => 'winter'
        ];

        $semester->update([
            'name' => $request->name,
            'semester_code' => strtoupper($request->semester_code),
            'academic_year' => (int) explode('-', $request->academic_year)[0], // Extract first year
            'period' => $periodMap[$request->semester_type],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'registration_start_date' => $request->registration_start,
            'registration_end_date' => $request->registration_end,
            'fee_payment_deadline' => $request->fee_due_date,
            'late_payment_deadline' => $request->late_fee_start_date,
            'grace_period_days' => $request->grace_period_days ?? 7,
            'max_credits_per_student' => $request->max_credits,
            'min_credits_per_student' => $request->min_credits,
            'status' => $request->status === 'active' ? 'active' : 'upcoming',
        ]);

        return redirect()->route('admin.semesters.show', $semester)
            ->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified semester
     */
    public function destroy(Semester $semester): RedirectResponse
    {
        // Check if semester has enrollments or fee structures
        if ($semester->enrollments()->exists() || $semester->feeStructures()->exists()) {
            return back()->withErrors([
                'delete' => 'Cannot delete semester with existing enrollments or fee structures. Consider deactivating instead.'
            ]);
        }

        $semester->delete();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester deleted successfully.');
    }

    /**
     * Toggle semester status
     */
    public function toggleStatus(Semester $semester): RedirectResponse
    {
        $semester->update([
            'status' => $semester->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $semester->status === 'active' ? 'activated' : 'deactivated';
        
        return back()->with('success', "Semester {$status} successfully.");
    }

    /**
     * Display semester enrollments
     */
    public function enrollments(Request $request, Semester $semester): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = $semester->enrollments()->with(['student', 'course', 'feeStructure']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $courses = Course::where('is_active', true)->get();
        $statuses = ['active', 'completed', 'pending', 'cancelled', 'withdrawn'];

        return view('admin.semesters.enrollments', compact(
            'semester', 'enrollments', 'courses', 'statuses', 'admin'
        ));
    }

    /**
     * Duplicate semester
     */
    public function duplicate(Request $request, Semester $semester): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester_code' => 'required|string|max:20|unique:semesters,semester_code',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $newSemester = $semester->replicate();
        $newSemester->name = $request->name;
        $newSemester->semester_code = strtoupper($request->semester_code);
        $newSemester->academic_year = $request->academic_year;
        $newSemester->start_date = $request->start_date;
        $newSemester->end_date = $request->end_date;
        
        // Calculate other dates based on the new start date
        $daysDiff = $semester->registration_start->diffInDays($semester->start_date, false);
        $newSemester->registration_start_date = $newSemester->start_date->copy()->addDays($daysDiff);
        
        $daysDiff = $semester->registration_end->diffInDays($semester->start_date, false);
        $newSemester->registration_end_date = $newSemester->start_date->copy()->addDays($daysDiff);
        
        $daysDiff = $semester->fee_due_date->diffInDays($semester->start_date, false);
        $newSemester->fee_payment_deadline = $newSemester->start_date->copy()->addDays($daysDiff);
        
        if ($semester->late_fee_start_date) {
            $daysDiff = $semester->late_fee_start_date->diffInDays($semester->start_date, false);
            $newSemester->late_payment_deadline = $newSemester->start_date->copy()->addDays($daysDiff);
        }
        
        $newSemester->status = 'inactive'; // Start as inactive
        $newSemester->save();

        return redirect()->route('admin.semesters.show', $newSemester)
            ->with('success', 'Semester duplicated successfully.');
    }

    /**
     * Bulk update semesters
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate',
            'semester_ids' => 'required|array|min:1',
            'semester_ids.*' => 'exists:semesters,id',
        ]);

        $semesters = Semester::whereIn('id', $request->semester_ids);
        
        switch ($request->action) {
            case 'activate':
                $count = $semesters->update(['status' => 'active']);
                $message = "Activated {$count} semesters.";
                break;
                
            case 'deactivate':
                $count = $semesters->update(['status' => 'inactive']);
                $message = "Deactivated {$count} semesters.";
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Export semesters to CSV
     */
    public function export(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Semester::withCount(['enrollments', 'feeStructures']);
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        if ($request->filled('semester_type')) {
            $query->where('semester_type', $request->semester_type);
        }

        $semesters = $query->get();

        $filename = 'semesters-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($semesters) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Semester Code', 'Semester Name', 'Academic Year', 'Type',
                'Start Date', 'End Date', 'Registration Start', 'Registration End',
                'Fee Due Date', 'Total Enrollments', 'Fee Structures', 'Status'
            ]);

            // CSV data
            foreach ($semesters as $semester) {
                fputcsv($file, [
                    $semester->semester_code,
                    $semester->name,
                    $semester->academic_year,
                    $semester->semester_type,
                    $semester->start_date->format('Y-m-d'),
                    $semester->end_date->format('Y-m-d'),
                    $semester->registration_start_date->format('Y-m-d'),
                    $semester->registration_end_date->format('Y-m-d'),
                    $semester->fee_payment_deadline->format('Y-m-d'),
                    $semester->enrollments_count,
                    $semester->fee_structures_count,
                    $semester->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get semester statistics (AJAX)
     */
    public function getStats(Semester $semester)
    {
        $stats = [
            'total_enrollments' => $semester->enrollments()->count(),
            'active_enrollments' => $semester->enrollments()->where('status', 'active')->count(),
            'completed_enrollments' => $semester->enrollments()->where('status', 'completed')->count(),
            'pending_enrollments' => $semester->enrollments()->where('status', 'pending')->count(),
            'total_students' => $semester->enrollments()->distinct('student_id')->count(),
            'total_revenue' => $semester->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->sum('payments.amount'),
            'average_payment' => $semester->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->avg('payments.amount'),
            'enrollment_by_course' => $semester->enrollments()
                ->with('course')
                ->selectRaw('course_id, COUNT(*) as count')
                ->groupBy('course_id')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Check if registration is open
     */
    public function checkRegistrationStatus(Semester $semester)
    {
        $now = now();
        $isOpen = $semester->status === 'active' && 
                  $now->between($semester->registration_start_date, $semester->registration_end_date);

        return response()->json([
            'is_open' => $isOpen,
            'registration_start' => $semester->registration_start_date->toISOString(),
            'registration_end' => $semester->registration_end_date->toISOString(),
            'current_time' => $now->toISOString(),
        ]);
    }
}
