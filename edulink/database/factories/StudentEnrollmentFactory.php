<?php

namespace Database\Factories;

use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    protected $model = StudentEnrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'enrollment_status' => 'active',
            'enrollment_date' => now(),
            'total_fees_due' => $this->faker->randomFloat(2, 50000, 300000),
            'fees_paid' => $this->faker->randomFloat(2, 0, 300000),
        ];
    }

    /**
     * Indicate that the enrollment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_status' => 'active',
        ]);
    }

    /**
     * Indicate that the enrollment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_status' => 'completed',
        ]);
    }
}
