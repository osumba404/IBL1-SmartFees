<?php

namespace Tests\Feature\Auth;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_can_view_login_form()
    {
        $response = $this->get('/student/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.student.login');
    }

    /** @test */
    public function student_can_login_with_valid_credentials()
    {
        $student = Student::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $response = $this->post('/student/login', [
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($student, 'student');
    }

    /** @test */
    public function student_cannot_login_with_invalid_credentials()
    {
        Student::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/student/login', [
            'email' => 'student@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('student');
    }

    /** @test */
    public function inactive_student_cannot_login()
    {
        $student = Student::factory()->inactive()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/student/login', [
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('student');
    }

    /** @test */
    public function student_can_register()
    {
        $response = $this->post('/student/register', [
            'student_id' => 'EDU2024001',
            'email' => 'newstudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '254712345678',
            'enrollment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('students', [
            'email' => 'newstudent@example.com',
            'student_id' => 'EDU2024001',
        ]);
    }

    /** @test */
    public function student_can_logout()
    {
        $student = Student::factory()->create();
        
        $response = $this->actingAs($student, 'student')
            ->post('/student/logout');

        $response->assertRedirect('/student/login');
        $this->assertGuest('student');
    }

    /** @test */
    public function authenticated_student_can_access_dashboard()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student, 'student')
            ->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('student.dashboard');
    }

    /** @test */
    public function unauthenticated_student_cannot_access_dashboard()
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/student/login');
    }
}
