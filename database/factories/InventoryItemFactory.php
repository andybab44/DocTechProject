<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                => fake()->words(2, true),
            'description'         => fake()->optional()->sentence(),
            'quantity'            => fake()->numberBetween(5, 100),
            'unit'                => fake()->randomElement(['pcs', 'ml', 'g', 'box', 'pack']),
            'category'            => fake()->optional()->word(),
            'low_stock_threshold' => 5,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(['quantity' => 2, 'low_stock_threshold' => 5]);
    }

    public function outOfStock(): static
    {
        return $this->state(['quantity' => 0, 'low_stock_threshold' => 5]);
    }
}
