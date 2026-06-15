<?php

namespace Tests\Feature\WorkJob;

use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkJobTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // create (GET /work-jobs/create)
    // =========================================================================

    public function test_guest_cannot_access_create_form(): void
    {
        $this->get('/work-jobs/create')->assertRedirect(route('login'));
    }

    public function test_doctor_can_access_create_form(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/work-jobs/create')
            ->assertOk()
            ->assertViewIs('work-jobs.create');
    }

    public function test_admin_can_access_create_form(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/work-jobs/create')
            ->assertOk();
    }

    public function test_technician_can_access_create_form_but_not_store(): void
    {
        // GET is guarded only by auth; the 403 is enforced at store via FormRequest
        $this->actingAs(User::factory()->technician()->create())
            ->get('/work-jobs/create')
            ->assertOk();
    }

    // =========================================================================
    // store (POST /work-jobs)
    // =========================================================================

    public function test_guest_cannot_store_work_job(): void
    {
        $this->post('/work-jobs', [])->assertRedirect(route('login'));
    }

    public function test_technician_cannot_store_work_job(): void
    {
        $tech      = User::factory()->technician()->create();
        $otherTech = User::factory()->technician()->create();

        $this->actingAs($tech)
            ->post('/work-jobs', $this->validStorePayload($otherTech->id))
            ->assertForbidden();
    }

    public function test_doctor_can_store_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        $response = $this->actingAs($doctor)
            ->post('/work-jobs', $this->validStorePayload($tech->id));

        $job = \App\Models\WorkJob::where('title', 'Test Work Job')->latest()->first();
        $response->assertRedirect(route('work-jobs.show', $job))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('work_jobs', [
            'title'     => 'Test Work Job',
            'doctor_id' => $doctor->id,
        ]);
    }

    public function test_admin_can_store_work_job(): void
    {
        $admin = User::factory()->admin()->create();
        $tech  = User::factory()->technician()->create();

        $response = $this->actingAs($admin)
            ->post('/work-jobs', $this->validStorePayload($tech->id));

        $job = \App\Models\WorkJob::where('title', 'Test Work Job')->latest()->first();
        $response->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['title' => 'Test Work Job']);
    }

    public function test_store_requires_title(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        $this->actingAs($doctor)
            ->post('/work-jobs', array_merge($this->validStorePayload($tech->id), ['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_store_requires_future_or_today_date(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        $this->actingAs($doctor)
            ->post('/work-jobs', array_merge($this->validStorePayload($tech->id), [
                'scheduled_at' => now()->subDay()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors('scheduled_at');
    }

    public function test_store_requires_valid_technician_id(): void
    {
        $doctor = User::factory()->doctor()->create();

        $this->actingAs($doctor)
            ->post('/work-jobs', $this->validStorePayload(99999))
            ->assertSessionHasErrors('technician_id');
    }

    public function test_store_rejects_non_technician_as_assignee(): void
    {
        $doctor       = User::factory()->doctor()->create();
        $anotherDoctor = User::factory()->doctor()->create();

        $this->actingAs($doctor)
            ->post('/work-jobs', $this->validStorePayload($anotherDoctor->id))
            ->assertSessionHasErrors('technician_id');
    }

    // =========================================================================
    // show (GET /work-jobs/{id})
    // =========================================================================

    public function test_guest_cannot_view_work_job(): void
    {
        $job = WorkJob::factory()->create();

        $this->get("/work-jobs/{$job->id}")->assertRedirect(route('login'));
    }

    public function test_doctor_can_view_own_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor)
            ->get("/work-jobs/{$job->id}")
            ->assertOk()
            ->assertViewIs('work-jobs.show');
    }

    public function test_doctor_cannot_view_another_doctors_work_job(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();
        $job     = WorkJob::factory()->create(['doctor_id' => $doctor2->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor1)
            ->get("/work-jobs/{$job->id}")
            ->assertForbidden();
    }

    public function test_technician_can_view_assigned_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($tech)
            ->get("/work-jobs/{$job->id}")
            ->assertOk();
    }

    public function test_technician_cannot_view_unassigned_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech1  = User::factory()->technician()->create();
        $tech2  = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech1->id]);

        $this->actingAs($tech2)
            ->get("/work-jobs/{$job->id}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_work_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create();

        $this->actingAs($admin)
            ->get("/work-jobs/{$job->id}")
            ->assertOk();
    }

    // =========================================================================
    // edit (GET /work-jobs/{id}/edit)
    // =========================================================================

    public function test_guest_cannot_access_edit_form(): void
    {
        $job = WorkJob::factory()->create();

        $this->get("/work-jobs/{$job->id}/edit")->assertRedirect(route('login'));
    }

    public function test_doctor_can_access_edit_form_for_own_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor)
            ->get("/work-jobs/{$job->id}/edit")
            ->assertOk()
            ->assertViewIs('work-jobs.edit');
    }

    public function test_doctor_cannot_access_edit_form_for_another_doctors_job(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();
        $job     = WorkJob::factory()->create(['doctor_id' => $doctor2->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor1)
            ->get("/work-jobs/{$job->id}/edit")
            ->assertForbidden();
    }

    public function test_technician_can_access_edit_form_for_assigned_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($tech)
            ->get("/work-jobs/{$job->id}/edit")
            ->assertOk();
    }

    public function test_technician_cannot_access_edit_form_for_unassigned_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech1  = User::factory()->technician()->create();
        $tech2  = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech1->id]);

        $this->actingAs($tech2)
            ->get("/work-jobs/{$job->id}/edit")
            ->assertForbidden();
    }

    public function test_admin_can_access_edit_form_for_any_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create();

        $this->actingAs($admin)
            ->get("/work-jobs/{$job->id}/edit")
            ->assertOk();
    }

    // =========================================================================
    // update (PUT /work-jobs/{id})
    // =========================================================================

    public function test_guest_cannot_update_work_job(): void
    {
        $job = WorkJob::factory()->create();

        $this->put("/work-jobs/{$job->id}", [])->assertRedirect(route('login'));
    }

    public function test_doctor_can_update_own_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor)
            ->put("/work-jobs/{$job->id}", [
                'title'         => 'Updated Title',
                'scheduled_at'  => now()->addDays(10)->format('Y-m-d'),
                'technician_id' => $tech->id,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'title' => 'Updated Title']);
    }

    public function test_doctor_cannot_update_another_doctors_work_job(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();
        $job     = WorkJob::factory()->create(['doctor_id' => $doctor2->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor1)
            ->put("/work-jobs/{$job->id}", [
                'title'         => 'Hacked Title',
                'scheduled_at'  => now()->addDay()->format('Y-m-d'),
                'technician_id' => $tech->id,
            ])
            ->assertForbidden();
    }

    public function test_technician_can_update_status_of_assigned_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'       => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($tech)
            ->put("/work-jobs/{$job->id}", ['status' => WorkJobStatus::InProgress->value])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', [
            'id'     => $job->id,
            'status' => WorkJobStatus::InProgress->value,
        ]);
    }

    public function test_technician_cannot_update_unassigned_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech1  = User::factory()->technician()->create();
        $tech2  = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech1->id]);

        $this->actingAs($tech2)
            ->put("/work-jobs/{$job->id}", ['status' => WorkJobStatus::InProgress->value])
            ->assertForbidden();
    }

    public function test_admin_can_update_any_work_job(): void
    {
        $admin = User::factory()->admin()->create();
        $tech  = User::factory()->technician()->create();
        $job   = WorkJob::factory()->create(['technician_id' => $tech->id]);

        $this->actingAs($admin)
            ->put("/work-jobs/{$job->id}", [
                'title'         => 'Admin Updated',
                'scheduled_at'  => now()->addDays(10)->format('Y-m-d'),
                'technician_id' => $tech->id,
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'title' => 'Admin Updated']);
    }

    // =========================================================================
    // destroy (DELETE /work-jobs/{id})
    // =========================================================================

    public function test_guest_cannot_delete_work_job(): void
    {
        $job = WorkJob::factory()->create();

        $this->delete("/work-jobs/{$job->id}")->assertRedirect(route('login'));
    }

    public function test_doctor_can_delete_own_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor)
            ->delete("/work-jobs/{$job->id}")
            ->assertRedirect(route('work-jobs.calendar'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('work_jobs', ['id' => $job->id]);
    }

    public function test_doctor_cannot_delete_another_doctors_work_job(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();
        $job     = WorkJob::factory()->create(['doctor_id' => $doctor2->id, 'technician_id' => $tech->id]);

        $this->actingAs($doctor1)
            ->delete("/work-jobs/{$job->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id]);
    }

    public function test_technician_cannot_delete_any_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->actingAs($tech)
            ->delete("/work-jobs/{$job->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id]);
    }

    public function test_admin_can_delete_any_work_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create();

        $this->actingAs($admin)
            ->delete("/work-jobs/{$job->id}")
            ->assertRedirect(route('work-jobs.calendar'));

        $this->assertDatabaseMissing('work_jobs', ['id' => $job->id]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function validStorePayload(int $technicianId): array
    {
        return [
            'title'         => 'Test Work Job',
            'description'   => 'A description.',
            'scheduled_at'  => now()->addDay()->format('Y-m-d'),
            'technician_id' => $technicianId,
        ];
    }
}
