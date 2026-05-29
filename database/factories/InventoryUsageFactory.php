<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryUsageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'inventory_item_id' => InventoryItem::factory(),
            'work_job_id'       => null,
            'used_by'           => User::factory()->technician(),
            'quantity_used'     => fake()->numberBetween(1, 5),
            'notes'             => fake()->optional()->sentence(),
        ];
    }
}
