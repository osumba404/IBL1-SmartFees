<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * Creates initial admin accounts for the system
     */
    public function run(): void
    {
        // Create Super Admin
        Admin::create([
            'name' => 'System Administrator',
            'email' => 'admin@edulink.ac.ke',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'can_manage_students' => true,
            'can_manage_courses' => true,
            'can_manage_payments' => true,
            'can_view_reports' => true,
            'can_approve_students' => true,
            'can_manage_fees' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Academic Admin
        Admin::create([
            'name' => 'Academic Administrator',
            'email' => 'academic@edulink.ac.ke',
            'password' => Hash::make('academic123'),
            'role' => 'admin',
            'can_manage_students' => true,
            'can_manage_courses' => true,
            'can_manage_payments' => false,
            'can_view_reports' => true,
            'can_approve_students' => true,
            'can_manage_fees' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Finance Admin
        Admin::create([
            'name' => 'Finance Administrator',
            'email' => 'finance@edulink.ac.ke',
            'password' => Hash::make('finance123'),
            'role' => 'admin',
            'can_manage_students' => false,
            'can_manage_courses' => false,
            'can_manage_payments' => true,
            'can_view_reports' => true,
            'can_approve_students' => false,
            'can_manage_fees' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "✅ Admin accounts created successfully!\n";
        echo "📧 Super Admin: admin@edulink.ac.ke (Password: admin123)\n";
        echo "📧 Academic Admin: academic@edulink.ac.ke (Password: academic123)\n";
        echo "📧 Finance Admin: finance@edulink.ac.ke (Password: finance123)\n";
        echo "⚠️  Please change these default passwords after first login!\n";
    }
}
