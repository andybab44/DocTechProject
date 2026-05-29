<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\WorkJob;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function store(StoreReviewRequest $request, WorkJob $workJob): RedirectResponse
    {
        $reviewer = auth()->user();

        abort_if(! $this->reviewService->canReview($reviewer, $workJob), 403);

        $this->reviewService->create($reviewer, $workJob, $request->validated());

        return redirect()
            ->route('work-jobs.show', $workJob)
            ->with('success', __('app.reviews.success_submitted'));
    }
}
