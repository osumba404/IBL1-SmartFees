<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Edulink SmartFees</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .button { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Edulink SmartFees!</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>
            
            <p>Welcome to Edulink International College Nairobi! We're excited to have you join our academic community.</p>
            
            <div class="info-box">
                <h3>Your Account Details</h3>
                <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Registration Date:</strong> {{ $student->created_at->format('F d, Y') }}</p>
            </div>
            
            <p>Your student account has been successfully created. You can now:</p>
            <ul>
                <li>View your fee structure and payment history</li>
                <li>Make secure online payments</li>
                <li>Download receipts and statements</li>
                <li>Update your profile information</li>
            </ul>
            
            <p style="text-align: center;">
                <a href="{{ url('/student/dashboard') }}" class="button">Access Your Dashboard</a>
            </p>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our student services team.</p>
            
            <p>Best regards,<br>
            Student Services Team<br>
            Edulink International College Nairobi</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Contact us: info@edulink.ac.ke | +254 700 000 000</p>
        </div>
    </div>
</body>
</html>