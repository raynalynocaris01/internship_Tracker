<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'IT401',
                'name' => 'Web Development Internship',
                'description' => 'Practical web development training',
                'units' => 3,
                'required_hours' => 500,
                'semester' => '1st',
                'school_year' => 2024,
                'status' => 'active'
            ],
            [
                'code' => 'IT402',
                'name' => 'Mobile App Development Internship',
                'description' => 'Mobile application development practicum',
                'units' => 3,
                'required_hours' => 500,
                'semester' => '2nd',
                'school_year' => 2024,
                'status' => 'active'
            ],
            [
                'code' => 'CS401',
                'name' => 'Software Engineering Internship',
                'description' => 'Software development lifecycle training',
                'units' => 3,
                'required_hours' => 600,
                'semester' => '1st',
                'school_year' => 2024,
                'status' => 'active'
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}