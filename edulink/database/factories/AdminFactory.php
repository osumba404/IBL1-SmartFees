<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '254' . $this->faker->numerify('##########'),
            'employee_id' => 'EMP' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'role' => 'admin',
            'is_active' => true,
            'department' => $this->faker->randomElement(['Finance', 'Academic', 'Administration']),
            'position' => $this->faker->jobTitle(),
            'can_manage_students' => false,
            'can_manage_courses' => false,
            'can_manage_payments' => false,
            'can_view_reports' => false,
        ];
    }

    /**
     * Indicate that the admin is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'super_admin',
            'can_manage_students' => true,
            'can_manage_courses' => true,
            'can_manage_payments' => true,
            'can_view_reports' => true,
            'can_manage_fees' => true,
        ]);
    }
}
