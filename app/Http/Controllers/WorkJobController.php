<?php

namespace App\Http\Controllers;

use App\Enums\WorkJobStatus;
use App\Http\Requests\StoreWorkJobRequest;
use App\Http\Requests\UpdateWorkJobRequest;
use App\Models\WorkJob;
use App\Services\WorkJobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WorkJobController extends Controller
{
    public function __construct(private readonly WorkJobService $workJobService) {}

    /**
     * Show the calendar for the given month.
     */
    public function calendar(Request $request): View
    {
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        $currentMonth = Carbon::create($year, $month, 1);
        $prevMonth    = $currentMonth->copy()->subMonth();
        $nextMonth    = $currentMonth->copy()->addMonth();

        $jobs = $this->workJobService->getForCalendar(auth()->user(), $year, $month);

        // Index jobs by day-of-month for easy template lookup
        $jobsByDay = $jobs->groupBy(fn (WorkJob $j) => $j->scheduled_at->day);

        return view('work-jobs.calendar', compact(
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'jobsByDay',
        ));
    }

    /**
     * Show the form to create a new work job.
     */
    public function create(Request $request): View
    {
        $technicians = $this->workJobService->getTechnicians();
        $date        = $request->query('date'); // pre-fill from calendar click

        return view('work-jobs.create', compact('technicians', 'date'));
    }

    /**
     * Store a new work job.
     */
    public function store(StoreWorkJobRequest $request): RedirectResponse
    {
        $this->workJobService->create(auth()->user(), $request->validated());

        return redirect()
            ->route('work-jobs.calendar')
            ->with('success', 'Work job created successfully.');
    }

    /**
     * Show a single work job.
     */
    public function show(WorkJob $workJob): View
    {
        $this->authorizeView($workJob);

        $workJob->load(['doctor', 'technician']);
        $statuses = WorkJobStatus::cases();

        return view('work-jobs.show', compact('workJob', 'statuses'));
    }

    /**
     * Show the edit form.
     */
    public function edit(WorkJob $workJob): View
    {
        $this->authorizeEdit($workJob);

        $workJob->load(['doctor', 'technician']);
        $technicians = $this->workJobService->getTechnicians();
        $statuses    = WorkJobStatus::cases();

        return view('work-jobs.edit', compact('workJob', 'technicians', 'statuses'));
    }

    /**
     * Update a work job.
     */
    public function update(UpdateWorkJobRequest $request, WorkJob $workJob): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isTechnician() && $workJob->technician_id !== $user->id) {
            abort(403);
        }

        if ($user->isDoctor() && $workJob->doctor_id !== $user->id) {
            abort(403);
        }

        $data = $request->validated();

        if ($user->isTechnician()) {
            $this->workJobService->updateStatus($workJob, WorkJobStatus::from($data['status']));
        } else {
            $this->workJobService->update($workJob, $data);
        }

        return redirect()
            ->route('work-jobs.show', $workJob)
            ->with('success', 'Work job updated successfully.');
    }

    /**
     * Delete a work job (doctor or admin only).
     */
    public function destroy(WorkJob $workJob): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isDoctor() && $workJob->doctor_id !== $user->id) {
            abort(403);
        }

        if ($user->isTechnician()) {
            abort(403);
        }

        $this->workJobService->delete($workJob);

        return redirect()
            ->route('work-jobs.calendar')
            ->with('success', 'Work job deleted.');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function authorizeView(WorkJob $workJob): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isDoctor() && $workJob->doctor_id === $user->id) {
            return;
        }

        if ($user->isTechnician() && $workJob->technician_id === $user->id) {
            return;
        }

        abort(403);
    }

    private function authorizeEdit(WorkJob $workJob): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isDoctor() && $workJob->doctor_id === $user->id) {
            return;
        }

        if ($user->isTechnician() && $workJob->technician_id === $user->id) {
            return; // technicians can edit status via edit page
        }

        abort(403);
    }
}
