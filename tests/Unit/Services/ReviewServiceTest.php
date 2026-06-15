<?php

namespace Tests\Unit\Services;

use App\Enums\WorkJobStatus;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkJob;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReviewService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReviewService();
    }

    // =========================================================================
    // canReview
    // =========================================================================

    public function test_doctor_can_review_done_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $this->assertTrue($this->service->canReview($doctor, $job));
    }

    public function test_technician_can_review_done_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $this->assertTrue($this->service->canReview($tech, $job));
    }

    public function test_doctor_can_review_cancelled_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Cancelled,
        ]);

        $this->assertTrue($this->service->canReview($doctor, $job));
    }

    public function test_cannot_review_pending_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::AwaitingAcceptance,
        ]);

        $this->assertFalse($this->service->canReview($doctor, $job));
    }

    public function test_cannot_review_in_progress_job(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InProgress,
        ]);

        $this->assertFalse($this->service->canReview($doctor, $job));
    }

    public function test_cannot_review_job_you_are_not_part_of(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();
        $job     = WorkJob::factory()->create([
            'doctor_id'    => $doctor1->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $this->assertFalse($this->service->canReview($doctor2, $job));
    }

    public function test_cannot_review_if_already_reviewed(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        Review::factory()->create([
            'reviewer_id' => $doctor->id,
            'reviewee_id' => $tech->id,
            'work_job_id' => $job->id,
        ]);

        $this->assertFalse($this->service->canReview($doctor, $job));
    }

    public function test_admin_cannot_review_job(): void
    {
        $admin = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $this->assertFalse($this->service->canReview($admin, $job));
    }

    // =========================================================================
    // existingReview
    // =========================================================================

    public function test_existing_review_returns_null_when_none(): void
    {
        $doctor = User::factory()->doctor()->create();
        $job    = WorkJob::factory()->create(['doctor_id' => $doctor->id, 'status' => WorkJobStatus::Delivered]);

        $this->assertNull($this->service->existingReview($doctor, $job));
    }

    public function test_existing_review_returns_review_when_present(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $review = Review::factory()->create([
            'reviewer_id' => $doctor->id,
            'reviewee_id' => $tech->id,
            'work_job_id' => $job->id,
        ]);

        $found = $this->service->existingReview($doctor, $job);

        $this->assertNotNull($found);
        $this->assertEquals($review->id, $found->id);
    }

    // =========================================================================
    // create
    // =========================================================================

    public function test_create_sets_reviewee_as_technician_when_reviewer_is_doctor(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $review = $this->service->create($doctor, $job, ['rating' => 4, 'comment' => 'Good work']);

        $this->assertEquals($doctor->id, $review->reviewer_id);
        $this->assertEquals($tech->id, $review->reviewee_id);
        $this->assertEquals(4, $review->rating);
        $this->assertEquals('Good work', $review->comment);
        $this->assertTrue($review->is_visible);
    }

    public function test_create_sets_reviewee_as_doctor_when_reviewer_is_technician(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();
        $job    = WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::Delivered,
        ]);

        $review = $this->service->create($tech, $job, ['rating' => 5, 'comment' => null]);

        $this->assertEquals($tech->id, $review->reviewer_id);
        $this->assertEquals($doctor->id, $review->reviewee_id);
        $this->assertEquals(5, $review->rating);
        $this->assertNull($review->comment);
    }

    // =========================================================================
    // averageRating
    // =========================================================================

    public function test_average_rating_returns_null_when_no_reviews(): void
    {
        $user = User::factory()->technician()->create();

        $this->assertNull($this->service->averageRating($user));
    }

    public function test_average_rating_calculates_correctly(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        Review::factory()->create(['reviewee_id' => $tech->id, 'reviewer_id' => $doctor->id, 'rating' => 4, 'is_visible' => true]);
        Review::factory()->create(['reviewee_id' => $tech->id, 'reviewer_id' => User::factory()->doctor()->create()->id, 'rating' => 2, 'is_visible' => true]);

        $this->assertEquals(3.0, $this->service->averageRating($tech));
    }

    public function test_average_rating_excludes_hidden_reviews(): void
    {
        $tech = User::factory()->technician()->create();

        Review::factory()->create(['reviewee_id' => $tech->id, 'reviewer_id' => User::factory()->doctor()->create()->id, 'rating' => 5, 'is_visible' => true]);
        Review::factory()->create(['reviewee_id' => $tech->id, 'reviewer_id' => User::factory()->doctor()->create()->id, 'rating' => 1, 'is_visible' => false]);

        $this->assertEquals(5.0, $this->service->averageRating($tech));
    }

    // =========================================================================
    // toggleVisibility
    // =========================================================================

    public function test_toggle_visibility_hides_visible_review(): void
    {
        $review = Review::factory()->create(['is_visible' => true]);

        $updated = $this->service->toggleVisibility($review);

        $this->assertFalse($updated->is_visible);
    }

    public function test_toggle_visibility_shows_hidden_review(): void
    {
        $review = Review::factory()->create(['is_visible' => false]);

        $updated = $this->service->toggleVisibility($review);

        $this->assertTrue($updated->is_visible);
    }

    // =========================================================================
    // delete
    // =========================================================================

    public function test_delete_removes_review_from_database(): void
    {
        $review = Review::factory()->create();

        $this->service->delete($review);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
