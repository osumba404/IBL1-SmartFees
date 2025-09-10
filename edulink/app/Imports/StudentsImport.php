<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['admission_number'])) {
                continue;
            }

            // Find course by name or code
            $course = Course::where('name', $row['course'])
                ->orWhere('code', $row['course_code'])
                ->first();

            // Prepare student data
            $studentData = [
                'admission_number' => $row['admission_number'],
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'] ?? null,
                'course_id' => $course->id ?? null,
                'gender' => $row['gender'] ?? null,
                'date_of_birth' => $row['date_of_birth'] ?? null,
                'address' => $row['address'] ?? null,
                'city' => $row['city'] ?? null,
                'state' => $row['state'] ?? null,
                'country' => $row['country'] ?? null,
                'is_active' => isset($row['status']) ? strtolower($row['status']) === 'active' : true,
                'password' => Hash::make($row['password'] ?? 'password'),
                'email_verified_at' => now(),
            ];

            // Update or create student
            Student::updateOrCreate(
                ['admission_number' => $row['admission_number']],
                $studentData
            );
        }
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'admission_number' => 'required|string|max:50|unique:students,admission_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email',
            'course' => 'required|string|exists:courses,name',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'status' => 'nullable|in:Active,Inactive',
        ];
    }
}
