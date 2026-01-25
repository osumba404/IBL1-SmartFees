<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_course()
    {
        $course = Course::factory()->create([
            'course_code' => 'CS101',
            'name' => 'Introduction to Computer Science',
        ]);

        $this->assertDatabaseHas('courses', [
            'course_code' => 'CS101',
            'name' => 'Introduction to Computer Science',
        ]);
    }

    /** @test */
    public function it_returns_full_title_with_level()
    {
        $course = Course::factory()->create([
            'name' => 'Computer Science',
            'level' => 'degree',
        ]);

        $this->assertEquals('Computer Science (Degree)', $course->full_title);
    }

    /** @test */
    public function it_has_many_enrollments()
    {
        $course = Course::factory()->create();
        $enrollment1 = StudentEnrollment::factory()->create(['course_id' => $course->id]);
        $enrollment2 = StudentEnrollment::factory()->create(['course_id' => $course->id]);

        $this->assertCount(2, $course->enrollments);
    }

    /** @test */
    public function it_can_check_if_course_is_available()
    {
        $availableCourse = Course::factory()->create([
            'available_from' => now()->subMonth(),
            'available_until' => now()->addYear(),
            'status' => 'active',
        ]);

        $unavailableCourse = Course::factory()->create([
            'available_from' => now()->addMonth(),
            'available_until' => now()->addYear(),
            'status' => 'active',
        ]);

        $this->assertTrue($availableCourse->isAvailable());
        $this->assertFalse($unavailableCourse->isAvailable());
    }

    /** @test */
    public function it_can_check_if_course_has_capacity()
    {
        $course = Course::factory()->create([
            'max_students' => 100,
            'current_enrollment' => 50,
        ]);

        $this->assertTrue($course->hasCapacity());
        $this->assertEquals(50, $course->availableSpots());
    }
}
