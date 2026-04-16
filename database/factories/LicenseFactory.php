<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LicenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'expires_at' => now()->addYear(),
            'is_active'  => true,
            'modules'    => [],
        ];
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function perpetual(): static
    {
        return $this->state(['expires_at' => null]);
    }
}
