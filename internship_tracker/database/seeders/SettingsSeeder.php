<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'school_name', 'value' => 'Internship Tracker System', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_required_hours', 'value' => '500', 'type' => 'integer', 'group' => 'internship'],
            ['key' => 'late_cutoff_time', 'value' => '08:30:00', 'type' => 'string', 'group' => 'attendance'],
            ['key' => 'lunch_break_duration', 'value' => '1', 'type' => 'integer', 'group' => 'attendance'],
            ['key' => 'qr_code_size', 'value' => '250', 'type' => 'integer', 'group' => 'qrcode'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}