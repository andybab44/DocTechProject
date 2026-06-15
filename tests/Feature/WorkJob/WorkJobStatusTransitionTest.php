<?php

namespace Tests\Feature\WorkJob;

use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkJobStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Technician transitions
    // =========================================================================

    public function test_technician_can_accept_awaiting_job(): void
    {
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create([
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($tech)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::InProgress->value,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::InProgress->value]);
    }

    public function test_technician_can_submit_in_progress_job_for_review(): void
    {
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create([
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InProgress,
        ]);

        $this->actingAs($tech)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::InReview->value,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::InReview->value]);
    }

    public function test_technician_cannot_skip_to_delivered(): void
    {
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create([
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InProgress,
        ]);

        $this->actingAs($tech)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::Delivered->value,
            ])
            ->assertForbidden();
    }

    public function test_technician_cannot_update_another_technicians_job(): void
    {
        $tech1 = User::factory()->technician()->create();
        $tech2 = User::factory()->technician()->create();
        $job   = WorkJob::factory()->create([
            'technician_id' => $tech1->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($tech2)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::InProgress->value,
            ])
            ->assertForbidden();
    }

    // =========================================================================
    // Doctor transitions
    // =========================================================================

    public function test_doctor_can_approve_in_review_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InReview,
        ]);

        $this->actingAs($doctor)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::ReadyForDelivery->value,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::ReadyForDelivery->value]);
    }

    public function test_doctor_can_request_revision(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InReview,
        ]);

        $this->actingAs($doctor)
            ->put(route('work-jobs.update', $job), [
                'status'       => WorkJobStatus::NeedsRevision->value,
                'status_notes' => 'Please adjust the crown shape.',
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::NeedsRevision->value]);
        $this->assertDatabaseHas('work_job_status_history', [
            'work_job_id' => $job->id,
            'to_status'   => WorkJobStatus::NeedsRevision->value,
            'notes'       => 'Please adjust the crown shape.',
        ]);
    }

    public function test_doctor_cannot_accept_a_job_technician_should_accept(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($doctor)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::InProgress->value,
            ])
            ->assertForbidden();
    }

    // =========================================================================
    // Admin transitions
    // =========================================================================

    public function test_admin_can_perform_any_transition(): void
    {
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create(['status' => WorkJobStatus::AwaitingAcceptance]);

        $this->actingAs($admin)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::Delivered->value,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::Delivered->value]);
    }

    // =========================================================================
    // Status history
    // =========================================================================

    public function test_status_change_creates_history_record(): void
    {
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create([
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($tech)
            ->put(route('work-jobs.update', $job), [
                'status' => WorkJobStatus::InProgress->value,
            ]);

        $this->assertDatabaseHas('work_job_status_history', [
            'work_job_id' => $job->id,
            'from_status' => WorkJobStatus::AwaitingAcceptance->value,
            'to_status'   => WorkJobStatus::InProgress->value,
            'changed_by'  => $tech->id,
        ]);
    }
}
