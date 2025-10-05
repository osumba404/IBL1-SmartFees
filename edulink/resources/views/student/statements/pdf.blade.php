<!DOCTYPE html>
<html>
<head>
    <title>Fee Statement - {{ $student->student_id }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            margin-bottom: 40px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header h2 {
            color: #6c757d;
            margin: 10px 0;
            font-size: 20px;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .info-section h3 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-section p {
            margin: 8px 0;
            line-height: 1.5;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table th, .table td { 
            padding: 12px 15px; 
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th { 
            background-color: #007bff;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .table tr:hover {
            background-color: #f8f9fa;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #e9ecef !important;
            border-top: 2px solid #007bff;
        }
        .print-btn { 
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        @media print { 
            .print-btn { display: none !important; }
            body { background: white; }
            .container { box-shadow: none; }
        }
        @media screen and (max-width: 0) {
            .print-btn { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" class="btn btn-primary">Print Statement</button>
        <button onclick="downloadPDF()" class="btn btn-primary">Download PDF</button>
        <button onclick="goBack()" class="btn btn-secondary">Close</button>
    </div>

    <script>
        function downloadPDF() {
            // Hide buttons before download
            const printBtn = document.querySelector('.print-btn');
            printBtn.style.display = 'none';
            
            // Create a clean version of the page
            const htmlContent = document.documentElement.outerHTML;
            
            // Create download link
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'fee-statement-{{ $student->student_id }}-{{ date("Y-m-d") }}.html';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            // Show buttons again
            printBtn.style.display = 'block';
        }
        
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ route("student.statements.index") }}';
            }
        }
    </script>

    <div class="container">
        <div class="header">
            <h1>Edulink International College Nairobi</h1>
            <h2>Official Fee Statement</h2>
            <p>Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        </div>

        <div class="info-section">
            <h3>Student Information</h3>
            <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
            <p><strong>Full Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>Email Address:</strong> {{ $student->email }}</p>
            <p><strong>Phone Number:</strong> {{ $student->phone ?? 'Not provided' }}</p>
        </div>

        <div class="info-section">
            <h3>Enrollment Details</h3>
            <p><strong>Course:</strong> {{ $enrollment->course->name }}</p>
            <p><strong>Course Code:</strong> {{ $enrollment->course->course_code }}</p>
            <p><strong>Semester:</strong> {{ $enrollment->semester->name ?? 'N/A' }}</p>
            <p><strong>Enrollment Number:</strong> {{ $enrollment->enrollment_number }}</p>
            <p><strong>Enrollment Date:</strong> {{ $enrollment->enrollment_date->format('F d, Y') }}</p>
            <p><strong>Academic Year:</strong> {{ $enrollment->semester->academic_year ?? date('Y') }}</p>
        </div>

    <div class="fee-summary">
        <h3>Fee Summary</h3>
        @php
            $totalFees = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 50000);
            $paidAmount = $enrollment->fees_paid ?? 0;
            $outstanding = $totalFees - $paidAmount;
        @endphp
        <table class="table">
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (KSh)</th>
            </tr>
            <tr>
                <td>Total Fees Due</td>
                <td style="text-align: right;">{{ number_format($totalFees, 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td style="text-align: right; color: #28a745;">{{ number_format($paidAmount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Outstanding Balance</td>
                <td style="text-align: right; color: {{ $outstanding > 0 ? '#dc3545' : '#28a745' }};">{{ number_format(max(0, $outstanding), 2) }}</td>
            </tr>
        </table>
    </div>

        @if($enrollment->payments->count() > 0)
        <div class="info-section">
            <h3>Payment History</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th style="text-align: right;">Amount (KSh)</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollment->payments->where('status', 'completed') as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span style="background: #e9ecef; padding: 2px 8px; border-radius: 12px; font-size: 11px; text-transform: uppercase;">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td>
                            <span style="background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 12px; font-size: 11px; text-transform: uppercase;">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td style="font-family: monospace; font-size: 12px;">{{ $payment->transaction_id ?? $payment->gateway_transaction_id ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="footer">
            <p><strong>This is an official computer-generated statement.</strong></p>
            <p>For any queries or clarifications, please contact our finance office.</p>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #dee2e6;">
            <p><strong>Edulink International College Nairobi</strong></p>
            <p>Email: support@edulink.ac.ke | Phone: +254 700 000 000</p>
            <p>Excellence in Education, Innovation in Learning</p>
        </div>
    </div>
</body>
</html>