<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id'   => Patient::factory(),
            'doctor_id'    => User::factory()->doctor(),
            'work_job_id'  => null,
            'scheduled_at' => Carbon::now()->addDays(fake()->numberBetween(1, 30)),
            'status'       => AppointmentStatus::Scheduled,
            'notes'        => fake()->optional()->sentence(),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(['status' => AppointmentStatus::Scheduled]);
    }

    public function completed(): static
    {
        return $this->state(['status' => AppointmentStatus::Completed]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => AppointmentStatus::Cancelled]);
    }
}
