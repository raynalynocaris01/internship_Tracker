<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin account
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'Administration',
                'email_verified_at' => now(),
            ]
        );

        // Create additional admin (optional)
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'IT Department',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('Admin Email: admin@gmail.com');
        $this->command->info('Admin Password: admin123');
    }
}