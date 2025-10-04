<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $totalCourses = Course::count();
        
        $monthlyRevenue = Payment::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('admin.reports.index', compact(
            'totalStudents',
            'totalPayments', 
            'pendingPayments',
            'totalCourses',
            'monthlyRevenue'
        ));
    }

    public function financial()
    {
        $payments = Payment::with(['student', 'semester'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.financial', compact('payments'));
    }

    public function students()
    {
        $students = Student::with(['enrollments.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.students', compact('students'));
    }
}