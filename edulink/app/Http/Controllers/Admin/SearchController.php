<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search functionality
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search students
        $students = Student::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->limit(5)->get();

        foreach ($students as $student) {
            $results[] = [
                'type' => 'student',
                'title' => $student->first_name . ' ' . $student->last_name,
                'subtitle' => $student->email . ' (' . $student->student_id . ')',
                'url' => route('admin.students.show', $student),
                'icon' => 'bi bi-person'
            ];
        }

        // Search payments
        $payments = Payment::with('student')
            ->whereHas('student', function($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%");
            })
            ->orWhere('transaction_id', 'LIKE', "%{$query}%")
            ->orWhere('amount', 'LIKE', "%{$query}%")
            ->limit(5)->get();

        foreach ($payments as $payment) {
            $results[] = [
                'type' => 'payment',
                'title' => 'Payment - KES ' . number_format($payment->amount, 2),
                'subtitle' => $payment->student->first_name . ' ' . $payment->student->last_name,
                'url' => route('admin.payments.show', $payment),
                'icon' => 'bi bi-credit-card'
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Advanced payment filters
     */
    public function paymentFilters(Request $request)
    {
        $query = Payment::with('student');

        if ($request->filled('student_name')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('first_name', 'LIKE', "%{$request->student_name}%")
                  ->orWhere('last_name', 'LIKE', "%{$request->student_name}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);

        return response()->json([
            'html' => view('admin.payments.table', compact('payments'))->render(),
            'pagination' => $payments->links()->render()
        ]);
    }

    /**
     * Student lookup system
     */
    public function studentLookup(Request $request)
    {
        $query = $request->get('q');
        
        $students = Student::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'student_id']);

        return response()->json($students->map(function($student) {
            return [
                'id' => $student->id,
                'text' => $student->first_name . ' ' . $student->last_name . ' (' . $student->student_id . ')',
                'email' => $student->email
            ];
        }));
    }

    /**
     * Student search
     */
    public function studentSearch(Request $request)
    {
        $query = Student::query();

        if ($request->filled('name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'LIKE', "%{$request->name}%")
                  ->orWhere('last_name', 'LIKE', "%{$request->name}%");
            });
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', 'LIKE', "%{$request->student_id}%");
        }

        if ($request->filled('email')) {
            $query->where('email', 'LIKE', "%{$request->email}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.students.table', compact('students'))->render(),
                'pagination' => $students->links()->render()
            ]);
        }

        return view('admin.search.students', compact('students'));
    }

    /**
     * Transaction search
     */
    public function transactionSearch(Request $request)
    {
        $query = Payment::with('student');

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'LIKE', "%{$request->transaction_id}%");
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'LIKE', "%{$request->reference}%");
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $transactions = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.payments.table', ['payments' => $transactions])->render(),
                'pagination' => $transactions->links()->render()
            ]);
        }

        return view('admin.search.transactions', compact('transactions'));
    }
}