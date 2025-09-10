<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FeeStructure;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Course::withCount(['students', 'enrollments', 'feeStructures']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $courses = $query->latest()->paginate(15)->withQueryString();
        
        // Get filter options
        $levels = Course::distinct()->pluck('level')->filter()->sort();
        $departments = Course::distinct()->pluck('department')->filter()->sort();

        return view('admin.courses.index', compact('courses', 'levels', 'departments', 'admin'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.courses.create', compact('admin'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:courses',
            'code' => 'required|string|max:20|unique:courses,course_code',
            'description' => 'nullable|string|max:1000',
            'department' => 'required|string|max:255',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'duration_months' => 'required|integer|min:1|max:120',
            'credit_hours' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'enrollment_capacity' => 'nullable|integer|min:1',
            'total_fee' => 'required|numeric|min:0',
            'enrollment_open' => 'required|in:yes,no',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $course = Course::create([
            'name' => $request->name,
            'course_code' => strtoupper($request->code),
            'description' => $request->description,
            'department' => $request->department,
            'level' => $request->level,
            'duration_months' => $request->duration_months,
            'credit_hours' => $request->credit_hours,
            'prerequisites' => $request->prerequisites ?? [],
            'max_students' => $request->enrollment_capacity,
            'total_fee' => $request->total_fee,
            'enrollment_open' => $request->enrollment_open === 'yes',
            'status' => $request->status,
        ]);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course): View
    {
        $admin = Auth::guard('admin')->user();
        
        // Get course statistics
        $stats = [
            'total_students' => $course->students()->count(),
            'active_enrollments' => $course->enrollments()->where('status', 'active')->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'total_revenue' => $course->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->sum('payments.amount'),
        ];

        // Get recent enrollments
        $recentEnrollments = $course->enrollments()
            ->with(['student', 'semester'])
            ->latest()
            ->take(10)
            ->get();

        // Get fee structures
        $feeStructures = $course->feeStructures()
            ->with('semester')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.courses.show', compact('course', 'stats', 'recentEnrollments', 'feeStructures', 'admin'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course): View
    {
        $admin = Auth::guard('admin')->user();
        
        // Get available courses for prerequisites (excluding current course)
        $availableCourses = Course::where('id', '!=', $course->id)
            ->where('status', 'active')
            ->get();

        return view('admin.courses.edit', compact('course', 'availableCourses', 'admin'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
            'code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'description' => 'nullable|string|max:1000',
            'department' => 'required|string|max:255',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'duration_months' => 'required|integer|min:1|max:120',
            'credit_hours' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'enrollment_capacity' => 'nullable|integer|min:1',
            'total_fee' => 'required|numeric|min:0',
            'enrollment_open' => 'required|in:yes,no',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        // Check for circular dependencies in prerequisites
        if ($request->prerequisites) {
            foreach ($request->prerequisites as $prerequisiteId) {
                if ($this->hasCircularDependency($course->id, $prerequisiteId)) {
                    return back()->withErrors([
                        'prerequisites' => 'Circular dependency detected. A course cannot be a prerequisite of itself.'
                    ]);
                }
            }
        }

        $course->update([
            'name' => $request->name,
            'course_code' => strtoupper($request->code),
            'description' => $request->description,
            'department' => $request->department,
            'level' => $request->level,
            'duration_months' => $request->duration_months,
            'credit_hours' => $request->credit_hours,
            'prerequisites' => $request->prerequisites ?? [],
            'max_students' => $request->enrollment_capacity,
            'total_fee' => $request->total_fee,
            'enrollment_open' => $request->enrollment_open === 'yes',
            'status' => $request->status,
        ]);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course): RedirectResponse
    {
        // Check if course has students or enrollments
        if ($course->students()->exists() || $course->enrollments()->exists()) {
            return back()->withErrors([
                'delete' => 'Cannot delete course with existing students or enrollments. Consider deactivating instead.'
            ]);
        }

        // Check if course is a prerequisite for other courses
        $dependentCourses = Course::whereJsonContains('prerequisites', $course->id)->count();
        if ($dependentCourses > 0) {
            return back()->withErrors([
                'delete' => 'Cannot delete course that is a prerequisite for other courses.'
            ]);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Toggle course status
     */
    public function toggleStatus(Course $course): RedirectResponse
    {
        $course->update([
            'status' => $course->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $course->status === 'active' ? 'activated' : 'deactivated';
        
        return back()->with('success', "Course {$status} successfully.");
    }

    /**
     * Display course students
     */
    public function students(Course $course): View
    {
        $admin = Auth::guard('admin')->user();
        
        $students = $course->students()
            ->with(['enrollments' => function($query) use ($course) {
                $query->where('course_id', $course->id);
            }])
            ->paginate(20);

        return view('admin.courses.students', compact('course', 'students', 'admin'));
    }

    /**
     * Display course fee structures
     */
    public function feeStructures(Course $course): View
    {
        $admin = Auth::guard('admin')->user();
        
        $feeStructures = $course->feeStructures()
            ->with('semester')
            ->latest()
            ->paginate(10);

        return view('admin.courses.fee-structures', compact('course', 'feeStructures', 'admin'));
    }

    /**
     * Duplicate course
     */
    public function duplicate(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:courses',
            'code' => 'required|string|max:20|unique:courses,course_code',
        ]);

        $newCourse = $course->replicate();
        $newCourse->name = $request->name;
        $newCourse->course_code = strtoupper($request->code);
        $newCourse->status = 'inactive'; // Start as inactive
        $newCourse->enrollment_open = false;
        $newCourse->save();

        return redirect()->route('admin.courses.show', $newCourse)
            ->with('success', 'Course duplicated successfully.');
    }

    /**
     * Bulk update courses
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,open_enrollment,close_enrollment',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $courses = Course::whereIn('id', $request->course_ids);
        
        switch ($request->action) {
            case 'activate':
                $count = $courses->update(['status' => 'active']);
                $message = "Activated {$count} courses.";
                break;
                
            case 'deactivate':
                $count = $courses->update(['status' => 'inactive']);
                $message = "Deactivated {$count} courses.";
                break;
                
            case 'open_enrollment':
                $count = $courses->update(['enrollment_open' => true]);
                $message = "Opened enrollment for {$count} courses.";
                break;
                
            case 'close_enrollment':
                $count = $courses->update(['enrollment_open' => false]);
                $message = "Closed enrollment for {$count} courses.";
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Export courses to CSV
     */
    public function export(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Course::withCount(['students', 'enrollments']);
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $courses = $query->get();

        $filename = 'courses-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($courses) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Course Code', 'Course Name', 'Department', 'Level', 
                'Duration (Months)', 'Credit Hours', 'Total Fee', 
                'Total Students', 'Active Enrollments', 'Status', 'Enrollment Open'
            ]);

            // CSV data
            foreach ($courses as $course) {
                fputcsv($file, [
                    $course->course_code,
                    $course->name,
                    $course->department,
                    $course->level,
                    $course->duration_months,
                    $course->credit_hours ?? 'N/A',
                    $course->total_fee,
                    $course->students_count,
                    $course->enrollments_count,
                    $course->status,
                    $course->enrollment_open ? 'Open' : 'Closed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check for circular dependency in prerequisites
     */
    protected function hasCircularDependency(int $courseId, int $prerequisiteId): bool
    {
        $prerequisite = Course::find($prerequisiteId);
        
        if (!$prerequisite || !$prerequisite->prerequisites) {
            return false;
        }

        // Direct circular dependency
        if (in_array($courseId, $prerequisite->prerequisites)) {
            return true;
        }

        // Indirect circular dependency
        foreach ($prerequisite->prerequisites as $prereqId) {
            if ($this->hasCircularDependency($courseId, $prereqId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get course statistics (AJAX)
     */
    public function getStats(Course $course)
    {
        $stats = [
            'total_students' => $course->students()->count(),
            'active_enrollments' => $course->enrollments()->where('status', 'active')->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'pending_enrollments' => $course->enrollments()->where('status', 'pending')->count(),
            'total_revenue' => $course->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->sum('payments.amount'),
            'average_payment' => $course->enrollments()
                ->join('payments', 'student_enrollments.id', '=', 'payments.student_enrollment_id')
                ->where('payments.status', 'completed')
                ->avg('payments.amount'),
        ];

        return response()->json($stats);
    }
}
