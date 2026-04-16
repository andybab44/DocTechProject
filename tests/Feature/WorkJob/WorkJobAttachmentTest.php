<?php

namespace Tests\Feature\WorkJob;

use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkJobAttachmentTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Upload (store)
    // -------------------------------------------------------------------------

    public function test_guest_cannot_upload_attachment(): void
    {
        Storage::fake('local');
        $job = WorkJob::factory()->create();

        $this->post(route('work-jobs.attachments.store', $job), [
            'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('login'));
    }

    public function test_doctor_can_upload_to_own_job(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_job_attachments', [
            'work_job_id' => $job->id,
            'uploaded_by' => $doctor->id,
            'original_name' => 'report.pdf',
        ]);
    }

    public function test_doctor_cannot_upload_to_another_doctors_job(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(); // owned by a different doctor

        $this->actingAs($doctor)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    public function test_technician_can_upload_to_assigned_job(): void
    {
        Storage::fake('local');
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create(['technician_id' => $tech->id]);

        $this->actingAs($tech)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('photo.jpg', 200, 'image/jpeg'),
            ])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('work_job_attachments', ['work_job_id' => $job->id, 'uploaded_by' => $tech->id]);
    }

    public function test_technician_cannot_upload_to_unassigned_job(): void
    {
        Storage::fake('local');
        $tech = User::factory()->technician()->create();
        $job  = WorkJob::factory()->create(); // assigned to a different technician

        $this->actingAs($tech)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('photo.jpg', 200, 'image/jpeg'),
            ])
            ->assertForbidden();
    }

    public function test_admin_can_upload_to_any_job(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create();

        $this->actingAs($admin)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf'),
            ])
            ->assertRedirect(route('work-jobs.show', $job));
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->post(route('work-jobs.attachments.store', $job), [
                'file' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_upload_requires_a_file(): void
    {
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->post(route('work-jobs.attachments.store', $job), [])
            ->assertSessionHasErrors('file');
    }

    // -------------------------------------------------------------------------
    // Download
    // -------------------------------------------------------------------------

    public function test_guest_cannot_download_attachment(): void
    {
        $attachment = WorkJobAttachment::factory()->create();

        $this->get(route('work-jobs.attachments.download', [$attachment->workJob, $attachment]))
            ->assertRedirect(route('login'));
    }

    public function test_doctor_can_download_attachment_from_own_job(): void
    {
        Storage::fake('local');
        $doctor     = User::factory()->doctor()->create();
        $job        = WorkJob::factory()->create(['doctor_id' => $doctor->id]);

        Storage::disk('local')->put("work-jobs/{$job->id}/attachments/test.pdf", 'file content');
        $attachment = WorkJobAttachment::factory()->create([
            'work_job_id'   => $job->id,
            'stored_name'   => "work-jobs/{$job->id}/attachments/test.pdf",
            'original_name' => 'test.pdf',
            'mime_type'     => 'application/pdf',
        ]);

        $this->actingAs($doctor)
            ->get(route('work-jobs.attachments.download', [$job, $attachment]))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=test.pdf');
    }

    public function test_doctor_cannot_download_from_another_doctors_job(): void
    {
        Storage::fake('local');
        $doctor     = User::factory()->doctor()->create();
        $job        = WorkJob::factory()->create(); // different doctor
        $attachment = WorkJobAttachment::factory()->create(['work_job_id' => $job->id]);

        $this->actingAs($doctor)
            ->get(route('work-jobs.attachments.download', [$job, $attachment]))
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_uploader_can_delete_own_attachment(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id]);

        Storage::disk('local')->put('work-jobs/1/attachments/file.pdf', 'content');
        $attachment = WorkJobAttachment::factory()->create([
            'work_job_id'  => $job->id,
            'uploaded_by'  => $doctor->id,
            'stored_name'  => 'work-jobs/1/attachments/file.pdf',
        ]);

        $this->actingAs($doctor)
            ->delete(route('work-jobs.attachments.destroy', [$job, $attachment]))
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseMissing('work_job_attachments', ['id' => $attachment->id]);
    }

    public function test_technician_cannot_delete_attachment_they_did_not_upload(): void
    {
        Storage::fake('local');
        $tech       = User::factory()->technician()->create();
        $job        = WorkJob::factory()->create(['technician_id' => $tech->id]);
        $attachment = WorkJobAttachment::factory()->create(['work_job_id' => $job->id]); // uploaded by someone else

        $this->actingAs($tech)
            ->delete(route('work-jobs.attachments.destroy', [$job, $attachment]))
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_attachment(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $job   = WorkJob::factory()->create();

        Storage::disk('local')->put('work-jobs/1/attachments/file.pdf', 'content');
        $attachment = WorkJobAttachment::factory()->create([
            'work_job_id' => $job->id,
            'stored_name' => 'work-jobs/1/attachments/file.pdf',
        ]);

        $this->actingAs($admin)
            ->delete(route('work-jobs.attachments.destroy', [$job, $attachment]))
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseMissing('work_job_attachments', ['id' => $attachment->id]);
    }
}
