<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        
        if (!$query) {
            return response()->json(['results' => []]);
        }

        $results = [];

        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $results = $this->studentGlobalSearch($student, $query, $type);
        } elseif (Auth::guard('admin')->check()) {
            $results = $this->adminGlobalSearch($query, $type);
        }

        return response()->json(['results' => $results]);
    }

    private function studentGlobalSearch($student, $query, $type)
    {
        $results = [];

        if ($type === 'all' || $type === 'payments') {
            $payments = Payment::where('student_id', $student->id)
                ->where(function($q) use ($query) {
                    $q->where('transaction_id', 'LIKE', "%{$query}%")
                      ->orWhere('payment_reference', 'LIKE', "%{$query}%")
                      ->orWhere('amount', 'LIKE', "%{$query}%")
                      ->orWhere('status', 'LIKE', "%{$query}%");
                })
                ->with(['enrollment.course'])
                ->limit(5)
                ->get();

            foreach ($payments as $payment) {
                $results[] = [
                    'type' => 'payment',
                    'title' => 'Payment - KES ' . number_format($payment->amount, 2),
                    'subtitle' => $payment->transaction_id . ' • ' . ucfirst($payment->status),
                    'url' => route('student.payments.history') . '?search=' . $payment->transaction_id,
                    'icon' => 'bi-credit-card'
                ];
            }
        }

        if ($type === 'all' || $type === 'courses') {
            $enrollments = StudentEnrollment::where('student_id', $student->id)
                ->whereHas('course', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('course_code', 'LIKE', "%{$query}%");
                })
                ->with(['course'])
                ->limit(5)
                ->get();

            foreach ($enrollments as $enrollment) {
                $results[] = [
                    'type' => 'course',
                    'title' => $enrollment->course->name,
                    'subtitle' => $enrollment->course->course_code . ' • ' . ucfirst($enrollment->status),
                    'url' => route('student.enrollments.index'),
                    'icon' => 'bi-book'
                ];
            }
        }

        return $results;
    }

    private function adminGlobalSearch($query, $type)
    {
        $results = [];

        if ($type === 'all' || $type === 'students') {
            $students = Student::where(function($q) use ($query) {
                $q->where('student_id', 'LIKE', "%{$query}%")
                  ->orWhere('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

            foreach ($students as $student) {
                $results[] = [
                    'type' => 'student',
                    'title' => $student->first_name . ' ' . $student->last_name,
                    'subtitle' => $student->student_id . ' • ' . $student->email,
                    'url' => route('admin.students.show', $student->id),
                    'icon' => 'bi-person'
                ];
            }
        }

        if ($type === 'all' || $type === 'payments') {
            $payments = Payment::where(function($q) use ($query) {
                $q->where('transaction_id', 'LIKE', "%{$query}%")
                  ->orWhere('payment_reference', 'LIKE', "%{$query}%")
                  ->orWhere('amount', 'LIKE', "%{$query}%");
            })
            ->with(['student', 'enrollment.course'])
            ->limit(10)
            ->get();

            foreach ($payments as $payment) {
                $results[] = [
                    'type' => 'payment',
                    'title' => 'Payment - KES ' . number_format($payment->amount, 2),
                    'subtitle' => $payment->student->first_name . ' ' . $payment->student->last_name . ' • ' . $payment->transaction_id,
                    'url' => route('admin.payments.show', $payment->id),
                    'icon' => 'bi-credit-card'
                ];
            }
        }

        return $results;
    }

    public function advancedPaymentSearch(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $query = Payment::where('student_id', $student->id)
            ->with(['enrollment.course', 'enrollment.semester']);

        // Text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'LIKE', "%{$search}%")
                  ->orWhere('payment_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('enrollment.course', function($courseQuery) use ($search) {
                      $courseQuery->where('name', 'LIKE', "%{$search}%")
                                  ->orWhere('course_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Course filter
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('student.payments.advanced-search', compact('payments', 'student'));
    }
}