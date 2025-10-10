<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\PaymentNotification;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:test {student_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Create test notifications for a student';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->argument('student_id');
        
        if ($studentId) {
            $student = Student::find($studentId);
            if (!$student) {
                $this->error("Student with ID {$studentId} not found.");
                return 1;
            }
        } else {
            $student = Student::first();
            if (!$student) {
                $this->error("No students found in the database.");
                return 1;
            }
        }

        $this->info("Creating test notifications for {$student->first_name} {$student->last_name} (ID: {$student->id})");

        // Create various test notifications
        $notifications = [
            [
                'title' => 'Welcome Test',
                'message' => 'This is a test welcome notification.',
                'notification_type' => 'welcome',
            ],
            [
                'title' => 'Payment Reminder Test',
                'message' => 'This is a test payment reminder notification.',
                'notification_type' => 'payment_reminder',
            ],
            [
                'title' => 'Payment Success Test',
                'message' => 'This is a test payment success notification.',
                'notification_type' => 'payment_success',
            ],
            [
                'title' => 'Enrollment Test',
                'message' => 'This is a test enrollment notification.',
                'notification_type' => 'enrollment',
            ],
        ];

        foreach ($notifications as $notificationData) {
            PaymentNotification::create([
                'student_id' => $student->id,
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'notification_type' => $notificationData['notification_type'],
            ]);
            
            $this->info("Created: {$notificationData['title']}");
        }

        $this->info("Test notifications created successfully!");
        $this->info("You can view them at: /student/notifications");
        
        return 0;
    }
}