<?php

namespace Database\Factories;

use App\Models\DentalCase;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DentalCase>
 */
class DentalCaseFactory extends Factory
{
    protected $model = DentalCase::class;

    public function definition(): array
    {
        return [
            'patient_id'  => Patient::factory(),
            'doctor_id'   => User::factory()->doctor(),
            'title'       => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'notes'       => fake()->optional()->sentence(),
        ];
    }
}
