<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create super admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->command->warn('Super Admin role not found. Please run RoleSeeder first.');
            return;
        }

        // Create default super admin
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@aitutor.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@aitutor.com',
                'password' => Hash::make('admin123'), // Change this password after first login!
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ]
        );

        $this->command->info('Default admin created:');
        $this->command->info('Email: admin@aitutor.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the default password after first login!');
    }
}

