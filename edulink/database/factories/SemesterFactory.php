<?php

namespace Database\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endDate = (clone $startDate)->modify('+4 months');

        return [
            'semester_code' => 'SEM' . date('Y') . $this->faker->randomElement(['1', '2', '3']),
            'name' => $this->faker->randomElement(['Spring', 'Summer', 'Fall', 'Winter']) . ' ' . date('Y'),
            'academic_year' => date('Y') . '/' . (date('Y') + 1),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_start_date' => (clone $startDate)->modify('-1 month'),
            'registration_end_date' => $startDate,
            'status' => 'active',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the semester is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }
}
