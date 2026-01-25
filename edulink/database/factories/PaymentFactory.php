<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'student_enrollment_id' => StudentEnrollment::factory(),
            'payment_reference' => 'PAY' . strtoupper(Str::random(10)),
            'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'currency' => 'KES',
            'payment_method' => $this->faker->randomElement(['mpesa', 'stripe', 'bank_transfer', 'cash']),
            'payment_type' => $this->faker->randomElement(['tuition', 'registration', 'examination', 'library']),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'payment_date' => now(),
            'is_verified' => false,
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'is_verified' => true,
            'verified_at' => now(),
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the payment is via M-Pesa.
     */
    public function mpesa(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'mpesa',
            'mpesa_phone_number' => '254' . $this->faker->numerify('##########'),
            'mpesa_receipt_number' => $this->faker->bothify('???####'),
        ]);
    }

    /**
     * Indicate that the payment is via Stripe.
     */
    public function stripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'stripe',
            'stripe_payment_intent_id' => 'pi_' . strtolower(Str::random(24)),
            'stripe_charge_id' => 'ch_' . strtolower(Str::random(24)),
        ]);
    }
}
