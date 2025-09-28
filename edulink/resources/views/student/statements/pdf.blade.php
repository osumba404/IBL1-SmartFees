<!DOCTYPE html>
<html>
<head>
    <title>Fee Statement - {{ $enrollment->enrollment_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Edulink International College Nairobi</h2>
        <h3>Fee Statement</h3>
    </div>

    <div class="details">
        <p><strong>Student:</strong> {{ auth('student')->user()->name }}</p>
        <p><strong>Enrollment #:</strong> {{ $enrollment->enrollment_number }}</p>
        <p><strong>Course:</strong> {{ $enrollment->course->name }}</p>
        <p><strong>Semester:</strong> {{ $enrollment->semester->name }}</p>
        <p><strong>Date:</strong> {{ now()->format('F d, Y') }}</p>
    </div>

    <h4>Fee Breakdown</h4>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (KSh)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollment->feeStructure->getAttributes() as $key => $value)
                @if(str_ends_with($key, '_fee') && is_numeric($value) && $value > 0)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', substr($key, 0, -4))) }}</td>
                        <td class="text-right">{{ number_format($value, 2) }}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th>Total Fees</th>
                <th class="text-right">{{ number_format($enrollment->feeStructure->total_amount, 2) }}</th>
            </tr>
            <tr>
                <th>Amount Paid</th>
                <th class="text-right">{{ number_format($enrollment->fees_paid, 2) }}</th>
            </tr>
            <tr>
                <th>Outstanding Balance</th>
                <th class="text-right">{{ number_format($enrollment->outstanding_balance, 2) }}</th>
            </tr>
        </tbody>
    </table>

    @if($enrollment->payments->count() > 0)
    <div class="mt-4">
        <h4>Payment History</h4>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th class="text-right">Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollment->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->reference_number }}</td>
                        <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer mt-4" style="margin-top: 40px;">
        <p>This is a computer-generated statement. No signature is required.</p>
        <p>For any inquiries, please contact the finance department.</p>
    </div>
</body>
</html>