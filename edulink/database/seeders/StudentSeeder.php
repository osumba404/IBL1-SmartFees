<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'student_id' => 'EDU001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@student.edulink.ac.ke',
                'phone' => '254712345678',
                'status' => 'active',
                'enrollment_date' => now()->subDays(30),
                'password' => Hash::make('password123'),
            ],
            [
                'student_id' => 'EDU002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@student.edulink.ac.ke',
                'phone' => '254723456789',
                'status' => 'active',
                'enrollment_date' => now()->subDays(25),
                'password' => Hash::make('password123'),
            ],
            [
                'student_id' => 'EDU003',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@student.edulink.ac.ke',
                'phone' => '254734567890',
                'status' => 'inactive',
                'enrollment_date' => now()->subDays(20),
                'password' => Hash::make('password123'),
            ],
            [
                'student_id' => 'EDU004',
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@student.edulink.ac.ke',
                'phone' => '254745678901',
                'status' => 'active',
                'enrollment_date' => now()->subDays(15),
                'password' => Hash::make('password123'),
            ],
            [
                'student_id' => 'EDU005',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@student.edulink.ac.ke',
                'phone' => '254756789012',
                'status' => 'suspended',
                'enrollment_date' => now()->subDays(10),
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }
    }
}