<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function index(): View
    {
        $reviews = $this->reviewService->paginate();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleVisibility(Review $review): RedirectResponse
    {
        $this->reviewService->toggleVisibility($review);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', __('app.reviews.success_visibility_updated'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->reviewService->delete($review);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', __('app.reviews.success_deleted'));
    }
}
