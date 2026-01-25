<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_payment()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'student_enrollment_id' => $enrollment->id,
            'amount' => 50000.00,
            'payment_method' => 'mpesa',
        ]);

        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 50000.00,
            'payment_method' => 'mpesa',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_student()
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $this->assertEquals($student->id, $payment->student->id);
    }

    /** @test */
    public function it_belongs_to_an_enrollment()
    {
        $enrollment = StudentEnrollment::factory()->create();
        $payment = Payment::factory()->create(['student_enrollment_id' => $enrollment->id]);

        $this->assertEquals($enrollment->id, $payment->enrollment->id);
    }

    /** @test */
    public function it_can_generate_payment_reference()
    {
        $reference = Payment::generatePaymentReference();

        $this->assertStringStartsWith('PAY', $reference);
        $this->assertGreaterThan(10, strlen($reference));
    }

    /** @test */
    public function it_can_mark_payment_as_completed()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $payment->markAsCompleted();

        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->processed_at);
    }

    /** @test */
    public function it_can_mark_payment_as_failed()
    {
        $payment = Payment::factory()->create(['status' => 'processing']);

        $payment->markAsFailed('Payment declined');

        $this->assertEquals('failed', $payment->status);
        $this->assertStringContainsString('Payment declined', $payment->admin_notes ?? '');
    }

    /** @test */
    public function it_can_check_if_payment_is_verified()
    {
        $verifiedPayment = Payment::factory()->completed()->create();
        $unverifiedPayment = Payment::factory()->create(['is_verified' => false]);

        $this->assertTrue($verifiedPayment->is_verified);
        $this->assertFalse($unverifiedPayment->is_verified);
    }
}
