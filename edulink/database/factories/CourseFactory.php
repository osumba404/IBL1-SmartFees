<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_code' => strtoupper($this->faker->bothify('??###')),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'level' => $this->faker->randomElement(['certificate', 'diploma', 'degree', 'masters', 'phd']),
            'type' => $this->faker->randomElement(['full_time', 'part_time', 'online', 'hybrid']),
            'duration_months' => $this->faker->numberBetween(12, 48),
            'credit_hours' => $this->faker->numberBetween(30, 180),
            'department' => $this->faker->randomElement(['Computer Science', 'Business', 'Engineering', 'Arts']),
            'faculty' => $this->faker->randomElement(['Applied Sciences', 'Business', 'Engineering']),
            'total_fee' => $this->faker->randomFloat(2, 100000, 500000),
            'registration_fee' => $this->faker->randomFloat(2, 5000, 20000),
            'examination_fee' => $this->faker->randomFloat(2, 10000, 50000),
            'library_fee' => $this->faker->randomFloat(2, 5000, 20000),
            'lab_fee' => $this->faker->randomFloat(2, 10000, 50000),
            'allows_installments' => true,
            'max_installments' => $this->faker->numberBetween(2, 6),
            'minimum_deposit_percentage' => $this->faker->randomFloat(2, 20, 40),
            'status' => 'active',
            'available_from' => now()->subMonths(6),
            'available_until' => now()->addYears(2),
            'max_students' => $this->faker->numberBetween(50, 200),
            'current_enrollment' => 0,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }
}
