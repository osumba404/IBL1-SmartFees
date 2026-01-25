<?php

namespace Tests\Unit\Models;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_student()
    {
        $student = Student::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        $this->assertDatabaseHas('students', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /** @test */
    public function it_returns_full_name_attribute()
    {
        $student = Student::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Michael Doe', $student->full_name);
    }

    /** @test */
    public function it_calculates_outstanding_balance()
    {
        $student = Student::factory()->create([
            'total_fees_owed' => 100000.00,
            'total_fees_paid' => 60000.00,
        ]);

        $this->assertEquals(40000.00, $student->outstanding_balance);
    }

    /** @test */
    public function it_has_many_enrollments()
    {
        $student = Student::factory()->create();
        $enrollment1 = StudentEnrollment::factory()->create(['student_id' => $student->id]);
        $enrollment2 = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $this->assertCount(2, $student->enrollments);
        $this->assertTrue($student->enrollments->contains($enrollment1));
        $this->assertTrue($student->enrollments->contains($enrollment2));
    }

    /** @test */
    public function it_has_many_payments()
    {
        $student = Student::factory()->create();
        $payment1 = Payment::factory()->create(['student_id' => $student->id]);
        $payment2 = Payment::factory()->create(['student_id' => $student->id]);

        $this->assertCount(2, $student->payments);
        $this->assertTrue($student->payments->contains($payment1));
        $this->assertTrue($student->payments->contains($payment2));
    }

    /** @test */
    public function it_can_check_if_student_has_outstanding_fees()
    {
        $studentWithFees = Student::factory()->withOutstandingFees()->create();
        $studentWithoutFees = Student::factory()->create([
            'total_fees_owed' => 100000.00,
            'total_fees_paid' => 100000.00,
            'has_outstanding_fees' => false,
        ]);

        $this->assertTrue($studentWithFees->has_outstanding_fees);
        $this->assertFalse($studentWithoutFees->has_outstanding_fees);
    }

    /** @test */
    public function it_hides_password_in_serialization()
    {
        $student = Student::factory()->create();
        $array = $student->toArray();

        $this->assertArrayNotHasKey('password', $array);
    }
}
