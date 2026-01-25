<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => 'EDU' . date('Y') . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'phone' => '254' . $this->faker->numerify('##########'),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'national_id' => $this->faker->optional()->numerify('########'),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => 'Kenya',
            'postal_code' => $this->faker->optional()->postcode(),
            'status' => 'active',
            'enrollment_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'total_fees_owed' => $this->faker->randomFloat(2, 50000, 500000),
            'total_fees_paid' => $this->faker->randomFloat(2, 0, 500000),
            'has_outstanding_fees' => false,
            'last_login_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the student has outstanding fees.
     */
    public function withOutstandingFees(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_outstanding_fees' => true,
            'total_fees_paid' => $attributes['total_fees_owed'] * 0.5,
        ]);
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
