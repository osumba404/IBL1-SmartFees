<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Display reports index
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }

    /**
     * Payment reports
     */
    public function payments(Request $request): View
    {
        $query = Payment::with(['student', 'enrollment.course']);
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $payments = $query->latest()->paginate(50);
        
        $stats = [
            'total_amount' => $query->sum('amount'),
            'completed_amount' => Payment::where('status', 'completed')->sum('amount'),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'total_count' => $query->count()
        ];
        
        return view('admin.reports.payments', compact('payments', 'stats'));
    }

    /**
     * Student reports
     */
    public function students(Request $request): View
    {
        $query = Student::with(['enrollments.course']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $students = $query->latest()->paginate(50);
        
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'pending_students' => Student::where('status', 'pending')->count(),
            'suspended_students' => Student::where('status', 'suspended')->count()
        ];
        
        return view('admin.reports.students', compact('students', 'stats'));
    }

    /**
     * Course reports
     */
    public function courses(): View
    {
        $courses = Course::withCount(['enrollments', 'activeEnrollments'])->get();
        
        return view('admin.reports.courses', compact('courses'));
    }

    /**
     * Financial reports
     */
    public function financial(): View
    {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalOutstanding = StudentEnrollment::sum('total_fees_due') - StudentEnrollment::sum('fees_paid');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_outstanding' => $totalOutstanding,
            'monthly_revenue' => $monthlyRevenue,
            'payment_methods' => Payment::where('status', 'completed')
                ->selectRaw('payment_method, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get()
        ];
        
        return view('admin.reports.financial', compact('stats'));
    }

    /**
     * Export payment reports
     */
    public function exportPayments(Request $request): Response
    {
        $payments = Payment::with(['student', 'enrollment.course'])->get();
        
        $csv = "Date,Student,Amount,Method,Status\n";
        foreach ($payments as $payment) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $payment->created_at->format('Y-m-d'),
                $payment->student->first_name . ' ' . $payment->student->last_name,
                $payment->amount,
                $payment->payment_method,
                $payment->status
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="payments-report.csv"');
    }

    /**
     * Export student reports
     */
    public function exportStudents(): Response
    {
        $students = Student::with(['enrollments.course'])->get();
        
        $csv = "Student ID,Name,Email,Phone,Status,Enrollments\n";
        foreach ($students as $student) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $student->student_id,
                $student->first_name . ' ' . $student->last_name,
                $student->email,
                $student->phone,
                $student->status,
                $student->enrollments->count()
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="students-report.csv"');
    }

    /**
     * Export financial reports
     */
    public function exportFinancial(): Response
    {
        $data = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_outstanding' => StudentEnrollment::sum('total_fees_due') - StudentEnrollment::sum('fees_paid'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="financial-report.json"');
    }
}