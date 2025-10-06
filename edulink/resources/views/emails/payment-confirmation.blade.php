<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; }
        .details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Confirmation</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>
            
            <p>We have successfully received your payment. Thank you for your prompt payment!</p>
            
            <div class="details">
                <h3>Payment Details</h3>
                <p><strong>Amount:</strong> <span class="amount">KES {{ number_format($payment->amount, 2) }}</span></p>
                <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                <p><strong>Reference:</strong> {{ $payment->payment_reference }}</p>
                <p><strong>Date:</strong> {{ $payment->created_at->format('F d, Y g:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
            </div>
            
            <p>This payment has been applied to your student account. You can view your updated balance and payment history by logging into your student portal.</p>
            
            <p>If you have any questions about this payment, please contact our finance office.</p>
            
            <p>Best regards,<br>
            Finance Department<br>
            Edulink International College Nairobi</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Contact us: finance@edulink.ac.ke | +254 700 000 000</p>
        </div>
    </div>
</body>
</html>