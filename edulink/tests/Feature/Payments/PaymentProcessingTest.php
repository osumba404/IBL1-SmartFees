<?php

namespace Tests\Feature\Payments;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_student_can_view_payment_page()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($student, 'student')
            ->get('/student/payment/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function student_can_initiate_mpesa_payment()
    {
        Queue::fake();
        
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $enrollment = StudentEnrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($student, 'student')
            ->post('/student/payments/initiate', [
                'enrollment_id' => $enrollment->id,
                'amount' => 5000.00,
                'payment_method' => 'mpesa',
                'phone_number' => '254712345678',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 5000.00,
            'payment_method' => 'mpesa',
        ]);
    }

    /** @test */
    public function student_can_view_payment_history()
    {
        $student = Student::factory()->create();
        Payment::factory()->count(3)->create(['student_id' => $student->id]);

        $response = $this->actingAs($student, 'student')
            ->get('/student/payments/history');

        $response->assertStatus(200);
        $response->assertViewHas('payments');
    }

    /** @test */
    public function student_can_view_payment_details()
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($student, 'student')
            ->get("/student/payments/{$payment->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function student_cannot_view_other_students_payments()
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student2->id]);

        $response = $this->actingAs($student1, 'student')
            ->get("/student/payments/{$payment->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function payment_requires_valid_enrollment()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student, 'student')
            ->post('/student/payments/initiate', [
                'enrollment_id' => 99999,
                'amount' => 5000.00,
                'payment_method' => 'mpesa',
            ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function payment_amount_must_be_positive()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($student, 'student')
            ->post('/student/payments/initiate', [
                'enrollment_id' => $enrollment->id,
                'amount' => -100,
                'payment_method' => 'mpesa',
            ]);

        $response->assertSessionHasErrors('amount');
    }
}
