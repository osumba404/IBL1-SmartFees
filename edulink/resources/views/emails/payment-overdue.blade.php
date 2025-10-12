<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overdue Payment Notice - Edulink SmartFees</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #e74c3c; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px 20px; background: #f9f9f9; }
        .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #e74c3c; }
        .overdue { font-size: 18px; font-weight: bold; color: #c0392b; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ OVERDUE PAYMENT NOTICE</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <h2>Dear {{ $student->first_name }} {{ $student->last_name }},</h2>
            
            <div class="warning">
                <p><strong>🚨 URGENT ACTION REQUIRED</strong></p>
                <p>Your payment is now <strong>{{ $daysOverdue }} day(s) overdue</strong>. Immediate payment is required to avoid further penalties.</p>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 5px solid #e74c3c;">
                <h3>Overdue Payment Details:</h3>
                <p><strong>Course:</strong> {{ $installment->paymentPlan->enrollment->course->name }}</p>
                <p><strong>Installment #:</strong> {{ $installment->installment_number }}</p>
                <p><strong>Original Amount:</strong> <span class="amount">KES {{ number_format($installment->amount, 2) }}</span></p>
                <p><strong>Days Overdue:</strong> <span class="overdue">{{ $daysOverdue }} day(s)</span></p>
                <p><strong>Original Due Date:</strong> {{ \Carbon\Carbon::parse($installment->due_date)->format('l, F j, Y') }}</p>
            </div>
            
            <div class="warning">
                <h4>⚠️ Important Notice:</h4>
                <ul>
                    <li>Late fees may apply for overdue payments</li>
                    <li>Continued non-payment may affect your enrollment status</li>
                    <li>Academic services may be suspended until payment is received</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('payment.create', ['enrollment_id' => $installment->paymentPlan->enrollment->id, 'amount' => $installment->amount]) }}" class="btn">
                    PAY NOW - URGENT
                </a>
            </div>
            
            <p><strong>Payment Methods Available:</strong></p>
            <ul>
                <li>M-Pesa Mobile Money (Instant)</li>
                <li>Credit/Debit Card (Instant)</li>
                <li>PayPal (Instant)</li>
                <li>Bank Transfer (1-2 business days)</li>
            </ul>
            
            <div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h4>💡 Need Help?</h4>
                <p>If you're experiencing financial difficulties, please contact our finance office immediately to discuss payment arrangements:</p>
                <p>📧 finance@edulink.ac.ke<br>📞 +254 700 000 000</p>
                <p><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
            </div>
            
            <p><strong>If you have already made this payment,</strong> please contact us immediately with your payment reference number.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Edulink International College Nairobi. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>