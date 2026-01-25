<?php

namespace Tests\Unit\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\MpesaService;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected $mpesaServiceMock;
    protected $stripeServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mpesaServiceMock = Mockery::mock(MpesaService::class);
        $this->stripeServiceMock = Mockery::mock(StripeService::class);

        $this->paymentService = new PaymentService(
            $this->mpesaServiceMock,
            $this->stripeServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_process_mpesa_payment()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $this->mpesaServiceMock
            ->shouldReceive('initiateSTKPush')
            ->once()
            ->andReturn([
                'success' => true,
                'checkout_request_id' => 'test_checkout_id',
            ]);

        $result = $this->paymentService->processPayment(
            $enrollment,
            5000.00,
            'mpesa',
            ['phone_number' => '254712345678']
        );

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 5000.00,
            'payment_method' => 'mpesa',
        ]);
    }

    /** @test */
    public function it_can_process_stripe_payment()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $this->stripeServiceMock
            ->shouldReceive('createPaymentIntent')
            ->once()
            ->andReturn([
                'success' => true,
                'client_secret' => 'test_client_secret',
                'payment_intent_id' => 'pi_test123',
            ]);

        $result = $this->paymentService->processPayment(
            $enrollment,
            10000.00,
            'stripe',
            []
        );

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 10000.00,
            'payment_method' => 'stripe',
        ]);
    }

    /** @test */
    public function it_can_process_bank_transfer_payment()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $result = $this->paymentService->processPayment(
            $enrollment,
            15000.00,
            'bank_transfer',
            [
                'bank_name' => 'Equity Bank',
                'bank_reference' => 'REF123456',
            ]
        );

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Equity Bank',
        ]);
    }

    /** @test */
    public function it_throws_exception_for_invalid_payment_method()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);

        $this->expectException(\InvalidArgumentException::class);

        $this->paymentService->processPayment(
            $enrollment,
            5000.00,
            'invalid_method',
            []
        );
    }

    /** @test */
    public function it_can_verify_payment()
    {
        $student = Student::factory()->create();
        $enrollment = StudentEnrollment::factory()->create(['student_id' => $student->id]);
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'student_enrollment_id' => $enrollment->id,
            'is_verified' => false,
        ]);

        $result = $this->paymentService->verifyPayment($payment->id, [
            'verified_by' => 1,
            'verification_notes' => 'Payment verified',
        ]);

        $this->assertTrue($result['success']);
        $payment->refresh();
        $this->assertTrue($payment->is_verified);
    }
}
