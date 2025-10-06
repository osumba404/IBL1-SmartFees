<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Large Payment Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .amount { font-size: 24px; font-weight: bold; color: #dc3545; }
        .details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Large Payment Alert</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <div class="alert">
                <p><strong>Alert:</strong> A large payment has been received and requires your attention.</p>
            </div>
            
            <div class="details">
                <h3>Payment Details</h3>
                <p><strong>Amount:</strong> <span class="amount">KES {{ number_format($payment->amount, 2) }}</span></p>
                <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_id }})</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                <p><strong>Reference:</strong> {{ $payment->payment_reference }}</p>
                <p><strong>Date:</strong> {{ $payment->created_at->format('F d, Y g:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
            </div>
            
            <div class="details">
                <h3>Student Information</h3>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Phone:</strong> {{ $student->phone }}</p>
                <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
            </div>
            
            <p>Please review this payment and take any necessary actions. Large payments may require additional verification or processing.</p>
            
            <p>Best regards,<br>
            System Administrator<br>
            Edulink SmartFees</p>
        </div>
        
        <div class="footer">
            <p>This is an automated alert. Please review the payment details carefully.</p>
            <p>Admin Portal: {{ url('/admin/dashboard') }}</p>
        </div>
    </div>
</body>
</html>