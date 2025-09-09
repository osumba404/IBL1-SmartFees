<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:create 
                            {--name= : Admin full name}
                            {--email= : Admin email address}
                            {--password= : Admin password}
                            {--role=admin : Admin role (super_admin or admin)}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating new admin account...');

        // Get input values
        $name = $this->option('name') ?: $this->ask('Admin full name');
        $email = $this->option('email') ?: $this->ask('Admin email address');
        $password = $this->option('password') ?: $this->secret('Admin password (min 8 characters)');
        $role = $this->option('role');

        // Validate role
        if (!in_array($role, ['super_admin', 'admin'])) {
            $role = $this->choice('Select admin role', ['super_admin', 'admin'], 1);
        }

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        // Set permissions based on role
        $permissions = $this->getPermissions($role);

        try {
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'can_manage_students' => $permissions['can_manage_students'],
                'can_manage_courses' => $permissions['can_manage_courses'],
                'can_manage_payments' => $permissions['can_manage_payments'],
                'can_view_reports' => $permissions['can_view_reports'],
                'can_approve_students' => $permissions['can_approve_students'],
                'can_manage_fees' => $permissions['can_manage_fees'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $this->info('✅ Admin account created successfully!');
            $this->table(['Field', 'Value'], [
                ['Name', $admin->name],
                ['Email', $admin->email],
                ['Role', $admin->role],
                ['Status', 'Active'],
            ]);

            $this->warn('⚠️  Please save the login credentials securely!');

        } catch (\Exception $e) {
            $this->error('Failed to create admin account: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Get default permissions based on role
     */
    private function getPermissions($role): array
    {
        if ($role === 'super_admin') {
            return [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_payments' => true,
                'can_view_reports' => true,
                'can_approve_students' => true,
                'can_manage_fees' => true,
            ];
        }

        // Default admin permissions
        return [
            'can_manage_students' => true,
            'can_manage_courses' => true,
            'can_manage_payments' => false,
            'can_view_reports' => true,
            'can_approve_students' => true,
            'can_manage_fees' => false,
        ];
    }
}
