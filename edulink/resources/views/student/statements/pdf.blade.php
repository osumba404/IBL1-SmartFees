<!DOCTYPE html>
<html>
<head>
    <title>Fee Statement - {{ $student->student_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .student-info, .enrollment-info { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .print-btn { margin: 20px 0; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" class="btn btn-primary">Print Statement</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="header">
        <h1>{{ config('app.college_name', 'Edulink International College') }}</h1>
        <h2>Fee Statement</h2>
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="student-info">
        <h3>Student Information</h3>
        <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
        <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
        <p><strong>Email:</strong> {{ $student->email }}</p>
    </div>

    <div class="enrollment-info">
        <h3>Enrollment Details</h3>
        <p><strong>Course:</strong> {{ $enrollment->course->name }}</p>
        <p><strong>Course Code:</strong> {{ $enrollment->course->course_code }}</p>
        <p><strong>Semester:</strong> {{ $enrollment->semester->name ?? 'N/A' }}</p>
        <p><strong>Enrollment Number:</strong> {{ $enrollment->enrollment_number }}</p>
        <p><strong>Enrollment Date:</strong> {{ $enrollment->enrollment_date->format('F d, Y') }}</p>
    </div>

    <div class="fee-summary">
        <h3>Fee Summary</h3>
        <table class="table">
            <tr>
                <th>Description</th>
                <th>Amount (KSh)</th>
            </tr>
            <tr>
                <td>Total Fees Due</td>
                <td>{{ number_format($enrollment->total_fees_due, 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td>{{ number_format($enrollment->fees_paid, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Outstanding Balance</td>
                <td>{{ number_format($enrollment->outstanding_balance, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($enrollment->payments->count() > 0)
    <div class="payment-history">
        <h3>Payment History</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount (KSh)</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollment->payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer" style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>This is a computer-generated statement. For any queries, contact the finance office.</p>
        <p>{{ config('app.college_name') }} | {{ config('app.college_phone') }} | {{ config('app.college_email') }}</p>
    </div>
</body>
</html>