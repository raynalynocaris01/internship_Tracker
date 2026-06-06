<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@internship.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'department' => 'Administration',
        ]);

        // Create Teachers with teacher_id
        User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@internship.com',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'teacher_id' => '1001',  // Changed from employee_id
            'department' => 'Computer Science',
        ]);

        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@school.edu',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'teacher_id' => '1002',
            'department' => 'Computer Science',
        ]);

        User::create([
            'name' => 'Robert Reyes',
            'email' => 'robert.reyes@school.edu',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'teacher_id' => '1003',
            'department' => 'Engineering',
        ]);

        // Create Students with student_id
        User::create([
            'name' => 'Jane Student',
            'email' => 'student@internship.com',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'student_id' => '2024001',
            'department' => 'Computer Science',
            'course' => 'BSIT',
            'year_level' => 3,
        ]);

        User::create([
            'name' => 'Mark Dela Cruz',
            'email' => 'mark.delacruz@student.edu',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'student_id' => '2024002',
            'department' => 'Computer Science',
            'course' => 'BSCS',
            'year_level' => 2,
        ]);

        User::create([
            'name' => 'Anna Reyes',
            'email' => 'anna.reyes@student.edu',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'student_id' => '2024003',
            'department' => 'Engineering',
            'course' => 'BSECE',
            'year_level' => 4,
        ]);

        // Create additional students and teachers using factories
        User::factory()->count(10)->student()->create();
        User::factory()->count(5)->teacher()->create();
    }
}