<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rules;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Student::with(['activeEnrollments.course', 'enrollments']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('course_id')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $students = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $courses = Course::where('is_active', true)->get();
        $statuses = ['active', 'pending_verification', 'suspended', 'inactive', 'graduated'];

        return view('admin.students.index', compact('students', 'courses', 'statuses', 'admin'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('is_active', true)->get();
        
        return view('admin.students.create', compact('courses', 'admin'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Student::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.Student::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:100'],
            'id_number' => ['required', 'string', 'max:50', 'unique:'.Student::class],
            'course_id' => ['required', 'exists:courses,id'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,pending_verification,suspended,inactive'],
        ]);

        $student = Student::create([
            'student_id' => Student::generateStudentId(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'id_number' => $request->id_number,
            'course_id' => $request->course_id,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'address' => $request->address,
            'status' => $request->status,
            'email_verified_at' => $request->status === 'active' ? now() : null,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student
     */
    public function show(Student $student): View
    {
        $admin = Auth::guard('admin')->user();
        
        $student->load([
            'enrollments.course',
            'enrollments.semester',
            'enrollments.feeStructure',
            'payments' => function($query) {
                $query->latest()->take(10);
            }
        ]);

        // Get financial summary
        $financialSummary = $student->getFinancialSummary();
        
        // Get recent activities
        $recentPayments = $student->payments()->with(['enrollment.course'])->latest()->take(5)->get();
        $activeEnrollments = $student->enrollments()->where('status', 'active')->with(['course', 'semester'])->get();

        return view('admin.students.show', compact('student', 'financialSummary', 'recentPayments', 'activeEnrollments', 'admin'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student): View
    {
        $admin = Auth::guard('admin')->user();
        $courses = Course::where('is_active', true)->get();
        
        return view('admin.students.edit', compact('student', 'courses', 'admin'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:students,email,' . $student->id],
            'phone' => ['required', 'string', 'max:20', 'unique:students,phone,' . $student->id],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:100'],
            'id_number' => ['required', 'string', 'max:50', 'unique:students,id_number,' . $student->id],
            'course_id' => ['required', 'exists:courses,id'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,pending_verification,suspended,inactive,graduated'],
        ]);

        $updateData = $request->only([
            'first_name', 'last_name', 'email', 'phone', 'date_of_birth',
            'gender', 'nationality', 'id_number', 'course_id',
            'emergency_contact_name', 'emergency_contact_phone', 'address', 'status'
        ]);

        // Set email verification if status is active
        if ($request->status === 'active' && !$student->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $student->update($updateData);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student): RedirectResponse
    {
        // Check if student has payments or enrollments
        if ($student->payments()->exists() || $student->enrollments()->exists()) {
            return back()->withErrors([
                'delete' => 'Cannot delete student with existing payments or enrollments. Consider suspending instead.'
            ]);
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Approve a student
     */
    public function approve(Student $student): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->can_approve_students && !$admin->is_super_admin) {
            abort(403, 'Insufficient permissions to approve students.');
        }

        $student->update([
            'status' => 'active',
            'email_verified_at' => now(),
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Student approved successfully.');
    }

    /**
     * Suspend a student
     */
    public function suspend(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $student->update([
            'status' => 'suspended',
            'suspension_reason' => $request->reason,
            'suspended_at' => now(),
        ]);

        return back()->with('success', 'Student suspended successfully.');
    }

    /**
     * Activate a suspended student
     */
    public function activate(Student $student): RedirectResponse
    {
        $student->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Student activated successfully.');
    }

    /**
     * Display student payments
     */
    public function payments(Student $student): View
    {
        $admin = Auth::guard('admin')->user();
        
        $payments = $student->payments()
            ->with(['enrollment.course', 'enrollment.semester'])
            ->latest()
            ->paginate(15);

        $paymentStats = [
            'total_payments' => $student->payments()->where('status', 'completed')->sum('amount'),
            'pending_payments' => $student->payments()->where('status', 'pending')->sum('amount'),
            'failed_payments' => $student->payments()->where('status', 'failed')->count(),
        ];

        return view('admin.students.payments', compact('student', 'payments', 'paymentStats', 'admin'));
    }

    /**
     * Display student enrollments
     */
    public function enrollments(Student $student): View
    {
        $admin = Auth::guard('admin')->user();
        
        $enrollments = $student->enrollments()
            ->with(['course', 'semester', 'feeStructure'])
            ->latest()
            ->paginate(10);

        return view('admin.students.enrollments', compact('student', 'enrollments', 'admin'));
    }

    /**
     * Create enrollment for student
     */
    public function createEnrollment(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'payment_plan' => 'required|in:full,installments',
        ]);

        // Check if student is already enrolled in this course for this semester
        $existingEnrollment = StudentEnrollment::where([
            'student_id' => $student->id,
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ])->first();

        if ($existingEnrollment) {
            return back()->withErrors([
                'enrollment' => 'Student is already enrolled in this course for the selected semester.'
            ]);
        }

        StudentEnrollment::create([
            'student_id' => $student->id,
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'fee_structure_id' => $request->fee_structure_id,
            'payment_plan' => $request->payment_plan,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }

    /**
     * Bulk actions for students
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,suspend,activate,delete',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'reason' => 'required_if:action,suspend|string|max:500',
        ]);

        $admin = Auth::guard('admin')->user();
        $students = Student::whereIn('id', $request->student_ids);
        $count = 0;

        switch ($request->action) {
            case 'approve':
                if (!$admin->can_approve_students && !$admin->is_super_admin) {
                    abort(403, 'Insufficient permissions to approve students.');
                }
                
                $count = $students->update([
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ]);
                break;

            case 'suspend':
                $count = $students->update([
                    'status' => 'suspended',
                    'suspension_reason' => $request->reason,
                    'suspended_at' => now(),
                ]);
                break;

            case 'activate':
                $count = $students->update([
                    'status' => 'active',
                    'suspension_reason' => null,
                    'suspended_at' => null,
                ]);
                break;

            case 'delete':
                // Only delete students without payments or enrollments
                $studentsToDelete = $students->whereDoesntHave('payments')
                    ->whereDoesntHave('enrollments')
                    ->get();
                
                $count = $studentsToDelete->count();
                $studentsToDelete->each->delete();
                break;
        }

        return back()->with('success', "Bulk action completed. {$count} students affected.");
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request)
    {
        $query = Student::with(['activeEnrollments.course', 'enrollments']);
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('course_id')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $students = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_export_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Admission No',
                'Full Name', 
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Gender',
                'Date of Birth',
                'National ID',
                'Status',
                'Enrollment Date',
                'Current Courses',
                'Total Fees Owed',
                'Total Fees Paid',
                'Outstanding Balance'
            ]);

            foreach ($students as $student) {
                // Get current courses
                $currentCourses = $student->activeEnrollments->pluck('course.name')->join(', ');
                
                // Calculate financial summary from enrollments
                $totalOwed = $student->enrollments->sum('total_fees_due');
                $totalPaid = $student->enrollments->sum('fees_paid');
                $outstanding = $totalOwed - $totalPaid;
                
                fputcsv($file, [
                    $student->student_id, // Admission No
                    $student->first_name . ' ' . $student->last_name, // Full Name
                    $student->first_name,
                    $student->last_name,
                    $student->email,
                    $student->phone ?? '',
                    $student->gender ?? '',
                    $student->date_of_birth?->format('Y-m-d') ?? '',
                    $student->national_id ?? '',
                    ucfirst($student->status),
                    $student->enrollment_date?->format('Y-m-d') ?? '',
                    $currentCourses ?: 'No active enrollments',
                    number_format($totalOwed, 2),
                    number_format($totalPaid, 2),
                    number_format($outstanding, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import students from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) !== count($header)) {
                    continue; // Skip malformed rows
                }

                $studentData = array_combine($header, $row);
                
                try {
                    // Validate required fields
                    if (empty($studentData['first_name']) || empty($studentData['last_name']) || empty($studentData['email'])) {
                        $errors[] = "Row skipped: Missing required fields (first_name, last_name, email)";
                        continue;
                    }

                    // Check if student already exists
                    if (Student::where('email', $studentData['email'])->exists()) {
                        $errors[] = "Row skipped: Student with email {$studentData['email']} already exists";
                        continue;
                    }

                    // Create student
                    Student::create([
                        'student_id' => Student::generateStudentId(),
                        'first_name' => $studentData['first_name'],
                        'last_name' => $studentData['last_name'],
                        'email' => $studentData['email'],
                        'phone' => $studentData['phone'] ?? null,
                        'password' => Hash::make($studentData['password'] ?? 'password123'),
                        'date_of_birth' => $studentData['date_of_birth'] ?? null,
                        'gender' => $studentData['gender'] ?? 'other',
                        'national_id' => $studentData['national_id'] ?? null,
                        'enrollment_date' => now()->toDateString(),
                        'status' => 'active',
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row skipped: Error processing student {$studentData['email']} - " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} students.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " rows had errors.";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
