<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'student',
            'department' => $this->faker->randomElement(['Computer Science', 'Engineering', 'Business']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'email' => 'admin@internship.com',
            'name' => 'System Administrator',
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'teacher',
            'teacher_id' => $this->faker->unique()->numberBetween(1000, 9999), // Changed from employee_id
            'department' => $this->faker->randomElement(['Computer Science', 'Engineering', 'Business']),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
            'student_id' => $this->faker->unique()->numberBetween(10000, 99999),
            'course' => $this->faker->randomElement(['BSIT', 'BSCS', 'BSIS']),
            'year_level' => $this->faker->numberBetween(1, 4),
        ]);
    }
}