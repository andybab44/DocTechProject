<?php

namespace Tests\Unit\Services;

use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use App\Services\WorkJobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkJobServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkJobService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WorkJobService();
    }

    // =========================================================================
    // getForCalendar
    // =========================================================================

    public function test_get_for_calendar_returns_all_jobs_for_admin(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        WorkJob::factory()->count(3)->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);

        $result = $this->service->getForCalendar($admin, now()->year, now()->month);

        $this->assertCount(3, $result);
    }

    public function test_get_for_calendar_returns_only_doctors_own_jobs(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();

        WorkJob::factory()->create(['doctor_id' => $doctor1->id, 'technician_id' => $tech->id, 'scheduled_at' => now()->startOfMonth()->addDays(5)]);
        WorkJob::factory()->create(['doctor_id' => $doctor2->id, 'technician_id' => $tech->id, 'scheduled_at' => now()->startOfMonth()->addDays(5)]);

        $result = $this->service->getForCalendar($doctor1, now()->year, now()->month);

        $this->assertCount(1, $result);
        $this->assertEquals($doctor1->id, $result->first()->doctor_id);
    }

    public function test_get_for_calendar_returns_only_assigned_jobs_for_technician(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech1  = User::factory()->technician()->create();
        $tech2  = User::factory()->technician()->create();

        WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech1->id, 'scheduled_at' => now()->startOfMonth()->addDays(5)]);
        WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech2->id, 'scheduled_at' => now()->startOfMonth()->addDays(5)]);

        $result = $this->service->getForCalendar($tech1, now()->year, now()->month);

        $this->assertCount(1, $result);
        $this->assertEquals($tech1->id, $result->first()->technician_id);
    }

    public function test_get_for_calendar_excludes_jobs_outside_requested_month(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::now()->subMonth(),
        ]);

        $result = $this->service->getForCalendar($admin, now()->year, now()->month);

        $this->assertCount(0, $result);
    }

    // =========================================================================
    // getTechnicians
    // =========================================================================

    public function test_get_technicians_returns_only_technician_users(): void
    {
        User::factory()->admin()->create();
        User::factory()->doctor()->create();
        User::factory()->count(2)->technician()->create();

        $result = $this->service->getTechnicians();

        $this->assertCount(2, $result);
        $result->each(fn ($u) => $this->assertTrue($u->isTechnician()));
    }

    public function test_get_technicians_orders_by_name(): void
    {
        User::factory()->technician()->create(['name' => 'Zebra Tech']);
        User::factory()->technician()->create(['name' => 'Alpha Tech']);

        $result = $this->service->getTechnicians();

        $this->assertEquals('Alpha Tech', $result->first()->name);
    }

    // =========================================================================
    // create
    // =========================================================================

    public function test_create_persists_work_job_with_pending_status(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        $job = $this->service->create($doctor, [
            'title'         => 'Test Job',
            'description'   => 'Some description',
            'scheduled_at'  => now()->addDay()->format('Y-m-d H:i:s'),
            'technician_id' => $tech->id,
        ]);

        $this->assertInstanceOf(WorkJob::class, $job);
        $this->assertEquals(WorkJobStatus::Pending, $job->status);
        $this->assertEquals($doctor->id, $job->doctor_id);
        $this->assertEquals($tech->id, $job->technician_id);
        $this->assertDatabaseHas('work_jobs', ['title' => 'Test Job']);
    }

    // =========================================================================
    // updateStatus
    // =========================================================================

    public function test_update_status_changes_the_job_status(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id, 'status' => WorkJobStatus::Pending]);

        $updated = $this->service->updateStatus($job, WorkJobStatus::InProgress);

        $this->assertEquals(WorkJobStatus::InProgress, $updated->status);
        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => WorkJobStatus::InProgress->value]);
    }

    // =========================================================================
    // update
    // =========================================================================

    public function test_update_changes_work_job_fields(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id, 'title' => 'Original']);

        $updated = $this->service->update($job, ['title' => 'Updated Title']);

        $this->assertEquals('Updated Title', $updated->title);
        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'title' => 'Updated Title']);
    }

    public function test_update_parses_scheduled_at_string(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $newDate = '2030-06-15';
        $updated = $this->service->update($job, ['scheduled_at' => $newDate]);

        $this->assertEquals('2030-06-15', $updated->scheduled_at->format('Y-m-d'));
    }

    // =========================================================================
    // delete
    // =========================================================================

    public function test_delete_removes_the_work_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);

        $this->service->delete($job);

        $this->assertDatabaseMissing('work_jobs', ['id' => $job->id]);
    }

    // =========================================================================
    // addAttachment / deleteAttachment
    // =========================================================================

    public function test_add_attachment_stores_file_and_creates_record(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'technician_id' => $tech->id]);
        $file   = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $attachment = $this->service->addAttachment($job, $doctor, $file);

        $this->assertInstanceOf(WorkJobAttachment::class, $attachment);
        $this->assertEquals('report.pdf', $attachment->original_name);
        $this->assertEquals($doctor->id, $attachment->uploaded_by);
        $this->assertEquals($job->id, $attachment->work_job_id);
        Storage::disk('local')->assertExists($attachment->stored_name);
    }

    public function test_delete_attachment_removes_file_and_record(): void
    {
        Storage::fake('local');
        $job  = WorkJob::factory()->create();
        Storage::disk('local')->put("work-jobs/{$job->id}/attachments/test.pdf", 'content');

        $attachment = WorkJobAttachment::factory()->create([
            'work_job_id' => $job->id,
            'stored_name' => "work-jobs/{$job->id}/attachments/test.pdf",
        ]);

        $this->service->deleteAttachment($attachment);

        $this->assertDatabaseMissing('work_job_attachments', ['id' => $attachment->id]);
        Storage::disk('local')->assertMissing("work-jobs/{$job->id}/attachments/test.pdf");
    }
}
