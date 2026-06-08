<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'BSIT-3A', 'code' => 'BSIT3A', 'year_level' => 3, 'course' => 'BSIT', 'max_students' => 40],
            ['name' => 'BSIT-3B', 'code' => 'BSIT3B', 'year_level' => 3, 'course' => 'BSIT', 'max_students' => 40],
            ['name' => 'BSCS-3A', 'code' => 'BSCS3A', 'year_level' => 3, 'course' => 'BSCS', 'max_students' => 35],
            ['name' => 'BSIS-3A', 'code' => 'BSIS3A', 'year_level' => 3, 'course' => 'BSIS', 'max_students' => 35],
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate($section);
        }
    }
}