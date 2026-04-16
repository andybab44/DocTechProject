<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkJobAttachment>
 */
class WorkJobAttachmentFactory extends Factory
{
    protected $model = WorkJobAttachment::class;

    public function definition(): array
    {
        return [
            'work_job_id'   => WorkJob::factory(),
            'uploaded_by'   => User::factory(),
            'original_name' => fake()->word() . '.pdf',
            'stored_name'   => 'work-jobs/1/attachments/' . fake()->uuid() . '.pdf',
            'mime_type'     => 'application/pdf',
            'size'          => fake()->numberBetween(1024, 1048576),
        ];
    }
}
