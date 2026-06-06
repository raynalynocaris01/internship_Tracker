<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('??###'),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'units' => $this->faker->numberBetween(2, 6),
            'required_hours' => $this->faker->numberBetween(300, 600),
            'semester' => $this->faker->randomElement(['1st', '2nd', 'Summer']),
            'school_year' => $this->faker->year(),
            'status' => 'active',
        ];
    }
}