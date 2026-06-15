<?php

namespace Tests\Feature\WorkJob;

use App\Enums\Module;
use App\Enums\WorkJobStatus;
use App\Models\License;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function userWithReviewLicense(string $role): User
    {
        $user = User::factory()->{$role}()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Reviews->value],
        ]);
        return $user;
    }

    private function doneJobFor(User $doctor, User $technician): WorkJob
    {
        return WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $technician->id,
            'status'        => WorkJobStatus::Delivered,
        ]);
    }

    // -------------------------------------------------------------------------
    // Module gating
    // -------------------------------------------------------------------------

    public function test_guest_cannot_submit_review(): void
    {
        $job = WorkJob::factory()->create();

        $this->post(route('reviews.store', $job), ['rating' => 4])
            ->assertRedirect(route('login'));
    }

    public function test_user_without_reviews_license_cannot_submit_review(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertForbidden();
    }

    public function test_user_with_expired_reviews_license_cannot_submit_review(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        License::factory()->for($doctor)->expired()->create(['modules' => [Module::Reviews->value]]);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Successful submission
    // -------------------------------------------------------------------------

    public function test_doctor_with_license_can_submit_review_for_done_job(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = $this->userWithReviewLicense('technician');
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 5, 'comment' => 'Excellent'])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('reviews', [
            'reviewer_id' => $doctor->id,
            'reviewee_id' => $tech->id,
            'work_job_id' => $job->id,
            'rating'      => 5,
            'comment'     => 'Excellent',
        ]);
    }

    public function test_technician_with_license_can_submit_review_for_done_job(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = $this->userWithReviewLicense('technician');
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($tech)
            ->post(route('reviews.store', $job), ['rating' => 3])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('reviews', [
            'reviewer_id' => $tech->id,
            'reviewee_id' => $doctor->id,
            'work_job_id' => $job->id,
            'rating'      => 3,
        ]);
    }

    public function test_review_can_be_submitted_without_comment(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = $this->userWithReviewLicense('technician');
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertRedirect(route('work-jobs.show', $job));

        $this->assertDatabaseHas('reviews', [
            'reviewer_id' => $doctor->id,
            'comment'     => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Authorization failures
    // -------------------------------------------------------------------------

    public function test_doctor_cannot_review_a_job_they_are_not_on(): void
    {
        $doctor1 = $this->userWithReviewLicense('doctor');
        $doctor2 = $this->userWithReviewLicense('doctor');
        $tech    = User::factory()->technician()->create();
        $job     = $this->doneJobFor($doctor1, $tech);

        $this->actingAs($doctor2)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertForbidden();
    }

    public function test_cannot_review_pending_job(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertForbidden();
    }

    public function test_cannot_review_in_progress_job(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InProgress,
        ]);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4])
            ->assertForbidden();
    }

    public function test_cannot_submit_duplicate_review_for_same_job(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        Review::factory()->create([
            'reviewer_id' => $doctor->id,
            'reviewee_id' => $tech->id,
            'work_job_id' => $job->id,
        ]);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 2])
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    public function test_rating_is_required(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), [])
            ->assertSessionHasErrors('rating');
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 6])
            ->assertSessionHasErrors('rating');

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 0])
            ->assertSessionHasErrors('rating');
    }

    public function test_comment_max_length_is_enforced(): void
    {
        $doctor = $this->userWithReviewLicense('doctor');
        $tech   = User::factory()->technician()->create();
        $job    = $this->doneJobFor($doctor, $tech);

        $this->actingAs($doctor)
            ->post(route('reviews.store', $job), ['rating' => 4, 'comment' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('comment');
    }
}
