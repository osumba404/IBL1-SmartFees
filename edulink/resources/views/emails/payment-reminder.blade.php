<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .amount { font-size: 24px; font-weight: bold; color: #dc3545; }
        .warning { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .button { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Reminder</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>
            
            <p>This is a friendly reminder that you have an outstanding balance on your student account.</p>
            
            <div class="warning">
                <p><strong>Outstanding Amount:</strong> <span class="amount">KES {{ number_format($amount, 2) }}</span></p>
                <p>Please make payment as soon as possible to avoid late fees and potential suspension of services.</p>
            </div>
            
            <p>Payment Options:</p>
            <ul>
                <li><strong>M-Pesa:</strong> Pay directly through your student portal</li>
                <li><strong>Bank Transfer:</strong> Use your student ID as reference</li>
                <li><strong>Card Payment:</strong> Secure online payment via Stripe</li>
                <li><strong>Cash Payment:</strong> Visit our finance office</li>
            </ul>
            
            <p style="text-align: center;">
                <a href="{{ url('/student/login') }}" class="button">Make Payment Now</a>
            </p>
            
            <p>If you have already made this payment, please allow 24-48 hours for processing. If you have any questions or need assistance, please contact our finance office.</p>
            
            <p>Thank you for your prompt attention to this matter.</p>
            
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