<?php

namespace Database\Factories;

use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WorkJob>
 */
class WorkJobFactory extends Factory
{
    protected $model = WorkJob::class;

    public function definition(): array
    {
        return [
            'title'         => fake()->sentence(3),
            'description'   => fake()->optional()->paragraph(),
            'scheduled_at'  => Carbon::now()->addDays(fake()->numberBetween(1, 30)),
            'status'        => WorkJobStatus::AwaitingAcceptance,
            'doctor_id'     => User::factory()->doctor(),
            'technician_id' => User::factory()->technician(),
        ];
    }
}
