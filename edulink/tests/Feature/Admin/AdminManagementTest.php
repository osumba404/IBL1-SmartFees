<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->superAdmin()->create();
    }

    /** @test */
    public function admin_can_view_students_list()
    {
        Student::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/students');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_payments_list()
    {
        Payment::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/payments');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_verify_payment()
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
            'is_verified' => false,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post("/admin/payments/{$payment->id}/verify", [
                'verification_notes' => 'Payment verified manually',
            ]);

        $response->assertRedirect();
        $payment->refresh();
        $this->assertTrue($payment->is_verified);
        $this->assertEquals('completed', $payment->status);
    }

    /** @test */
    public function admin_without_permission_cannot_manage_payments()
    {
        $admin = Admin::factory()->create([
            'can_manage_payments' => false,
        ]);
        $payment = Payment::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->post("/admin/payments/{$payment->id}/verify");

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_create_new_admin()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/admins', [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'admin',
                'can_manage_students' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admins', [
            'email' => 'newadmin@example.com',
        ]);
    }
}
