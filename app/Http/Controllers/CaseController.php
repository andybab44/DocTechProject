<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\DentalCase;
use App\Services\CaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function __construct(
        private readonly CaseService $caseService,
    ) {}

    public function index(): View
    {
        $cases = $this->caseService->paginate(auth()->user());

        return view('cases.index', compact('cases'));
    }

    public function create(): View
    {
        $patients = $this->caseService->getPatients();

        return view('cases.create', compact('patients'));
    }

    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $dentalCase = $this->caseService->create(auth()->user(), $request->validated());

        return redirect()
            ->route('cases.show', $dentalCase)
            ->with('success', 'Case created successfully.');
    }

    public function show(DentalCase $case): View
    {
        $this->authorizeAccess($case);

        $case->load([
            'patient',
            'doctor',
            'workJobs.technician',
            'workJobs.statusHistory',
            'appointments',
        ]);

        return view('cases.show', compact('case'));
    }

    public function edit(DentalCase $case): View
    {
        $this->authorizeAccess($case);

        $patients = $this->caseService->getPatients();

        return view('cases.edit', compact('case', 'patients'));
    }

    public function update(UpdateCaseRequest $request, DentalCase $case): RedirectResponse
    {
        $this->authorizeAccess($case);

        $this->caseService->update($case, $request->validated());

        return redirect()
            ->route('cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    public function destroy(DentalCase $case): RedirectResponse
    {
        $this->authorizeAccess($case);

        $this->caseService->delete($case);

        return redirect()
            ->route('cases.index')
            ->with('success', 'Case deleted.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function authorizeAccess(DentalCase $case): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isDoctor() && $case->doctor_id === $user->id) {
            return;
        }

        abort(403);
    }
}
