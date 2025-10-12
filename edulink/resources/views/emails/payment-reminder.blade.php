<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder - Edulink SmartFees</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px 20px; background: #f9f9f9; }
        .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #e74c3c; }
        .due-date { font-size: 18px; font-weight: bold; color: #f39c12; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Reminder</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <h2>Dear {{ $student->first_name }} {{ $student->last_name }},</h2>
            
            @if($daysUntilDue == 0)
                <p><strong>⚠️ URGENT: Your payment is due TODAY!</strong></p>
            @else
                <p>This is a friendly reminder that your installment payment is due in <strong>{{ $daysUntilDue }} day(s)</strong>.</p>
            @endif
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>Payment Details:</h3>
                <p><strong>Course:</strong> {{ $installment->paymentPlan->enrollment->course->name }}</p>
                <p><strong>Installment #:</strong> {{ $installment->installment_number }}</p>
                <p><strong>Amount Due:</strong> <span class="amount">KES {{ number_format($installment->amount, 2) }}</span></p>
                <p><strong>Due Date:</strong> <span class="due-date">{{ $dueDate->format('l, F j, Y') }}</span></p>
            </div>
            
            @if($daysUntilDue == 0)
                <p style="color: #e74c3c;"><strong>Please make your payment today to avoid late fees.</strong></p>
            @else
                <p>Please ensure your payment is made on or before the due date to avoid late fees.</p>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('payment.create', ['enrollment_id' => $installment->paymentPlan->enrollment->id, 'amount' => $installment->amount]) }}" class="btn">
                    Make Payment Now
                </a>
            </div>
            
            <p><strong>Payment Methods Available:</strong></p>
            <ul>
                <li>M-Pesa Mobile Money</li>
                <li>Credit/Debit Card</li>
                <li>PayPal</li>
                <li>Bank Transfer</li>
            </ul>
            
            <p>If you have already made this payment, please disregard this reminder.</p>
            
            <p>For any questions or assistance, please contact our finance office:</p>
            <p>📧 finance@edulink.ac.ke<br>📞 +254 700 000 000</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Edulink International College Nairobi. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>