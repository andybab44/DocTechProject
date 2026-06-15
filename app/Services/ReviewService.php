<?php

namespace App\Services;

use App\Enums\WorkJobStatus;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReviewService
{
    /**
     * Check whether $reviewer is eligible to review $workJob.
     *
     * Rules:
     *  - Job must be Done or Cancelled
     *  - Reviewer must be a participant (doctor or technician on the job)
     *  - Reviewer must not have already submitted a review for this job
     */
    public function canReview(User $reviewer, WorkJob $workJob): bool
    {
        if (! in_array($workJob->status, [WorkJobStatus::Delivered, WorkJobStatus::Cancelled], true)) {
            return false;
        }

        $isParticipant = $reviewer->isDoctor()
            ? $workJob->doctor_id === $reviewer->id
            : ($reviewer->isTechnician() && $workJob->technician_id === $reviewer->id);

        if (! $isParticipant) {
            return false;
        }

        return ! Review::where('reviewer_id', $reviewer->id)
            ->where('work_job_id', $workJob->id)
            ->exists();
    }

    /**
     * Return the reviewer's existing review for the job, or null.
     */
    public function existingReview(User $reviewer, WorkJob $workJob): ?Review
    {
        return Review::where('reviewer_id', $reviewer->id)
            ->where('work_job_id', $workJob->id)
            ->first();
    }

    /**
     * Create a review. The reviewee is inferred from the job and reviewer role.
     *
     * @param  array{rating: int, comment: ?string}  $data
     */
    public function create(User $reviewer, WorkJob $workJob, array $data): Review
    {
        // Doctor reviews the technician, technician reviews the doctor
        $revieweeId = $reviewer->isDoctor()
            ? $workJob->technician_id
            : $workJob->doctor_id;

        return Review::create([
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $revieweeId,
            'work_job_id' => $workJob->id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'] ?? null,
            'is_visible'  => true,
        ]);
    }

    /**
     * Return all visible reviews for a given work job.
     *
     * @return Collection<int, Review>
     */
    public function reviewsForJob(WorkJob $workJob): Collection
    {
        return Review::with(['reviewer'])
            ->where('work_job_id', $workJob->id)
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Return all visible reviews for a given reviewee (for their profile).
     *
     * @return Collection<int, Review>
     */
    public function forUser(User $user): Collection
    {
        return Review::with(['reviewer', 'workJob'])
            ->where('reviewee_id', $user->id)
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Return average visible rating for a user (null if no reviews).
     */
    public function averageRating(User $user): ?float
    {
        $avg = Review::where('reviewee_id', $user->id)
            ->where('is_visible', true)
            ->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * Admin: paginated list of all reviews (visible and hidden).
     *
     * @return LengthAwarePaginator<Review>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Review::with(['reviewer', 'reviewee', 'workJob'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Toggle the is_visible flag.
     */
    public function toggleVisibility(Review $review): Review
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return $review->fresh();
    }

    /**
     * Permanently delete a review.
     */
    public function delete(Review $review): void
    {
        $review->delete();
    }
}
