<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = Student::factory()->create();
    }

    /** @test */
    public function student_can_view_profile()
    {
        $response = $this->actingAs($this->student, 'student')
            ->get('/student/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function student_can_update_profile()
    {
        $response = $this->actingAs($this->student, 'student')
            ->put('/student/profile', [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'phone' => '254712345678',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('students', [
            'id' => $this->student->id,
            'first_name' => 'Updated',
        ]);
    }

    /** @test */
    public function student_can_view_enrollments()
    {
        StudentEnrollment::factory()->count(2)->create(['student_id' => $this->student->id]);

        $response = $this->actingAs($this->student, 'student')
            ->get('/student/enrollments');

        $response->assertStatus(200);
    }

    /** @test */
    public function student_can_view_courses()
    {
        Course::factory()->count(5)->create();

        $response = $this->actingAs($this->student, 'student')
            ->get('/student/courses');

        $response->assertStatus(200);
    }

    /** @test */
    public function student_can_enroll_in_course()
    {
        $course = Course::factory()->create();
        $semester = \App\Models\Semester::factory()->create();

        $response = $this->actingAs($this->student, 'student')
            ->post('/student/enrollments', [
                'course_id' => $course->id,
                'semester_id' => $semester->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $course->id,
        ]);
    }
}
