<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'          => fake()->name(),
            'date_of_birth' => fake()->optional()->date('Y-m-d', '-18 years'),
            'email'         => fake()->optional()->safeEmail(),
            'phone'         => fake()->optional()->phoneNumber(),
            'notes'         => fake()->optional()->sentence(),
        ];
    }
}
