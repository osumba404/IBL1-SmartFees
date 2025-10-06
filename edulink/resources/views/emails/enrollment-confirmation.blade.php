<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrollment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Enrollment Confirmation</h1>
            <p>Edulink International College Nairobi</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>
            
            <p class="success">Congratulations! Your enrollment has been confirmed.</p>
            
            <div class="details">
                <h3>Enrollment Details</h3>
                <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                <p><strong>Course:</strong> {{ $enrollment->course->name }}</p>
                <p><strong>Semester:</strong> {{ $enrollment->semester->name }}</p>
                <p><strong>Enrollment Date:</strong> {{ $enrollment->enrollment_date->format('F d, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($enrollment->status) }}</p>
            </div>
            
            <p>Next steps:</p>
            <ul>
                <li>Complete your fee payment to secure your spot</li>
                <li>Attend orientation sessions as scheduled</li>
                <li>Access your student portal for course materials</li>
                <li>Contact academic office for any questions</li>
            </ul>
            
            <p>Welcome to Edulink International College! We look forward to supporting your academic journey.</p>
            
            <p>Best regards,<br>
            Registrar's Office<br>
            Edulink International College Nairobi</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Contact us: registrar@edulink.ac.ke | +254 700 000 000</p>
        </div>
    </div>
</body>
</html>