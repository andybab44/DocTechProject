<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reviewer_id' => User::factory()->doctor(),
            'reviewee_id' => User::factory()->technician(),
            'work_job_id' => null,
            'rating'      => fake()->numberBetween(1, 5),
            'comment'     => fake()->optional()->sentence(),
            'is_visible'  => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }

    public function forJob(WorkJob $workJob): static
    {
        return $this->state(['work_job_id' => $workJob->id]);
    }
}
