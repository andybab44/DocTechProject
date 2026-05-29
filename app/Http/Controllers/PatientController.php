<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService) {}

    public function index(): View
    {
        $patients = $this->appointmentService->paginatePatients();

        return view('patients.index', compact('patients'));
    }

    public function show(Patient $patient): View
    {
        $appointments = $patient->appointments()
            ->with('doctor')
            ->orderByDesc('scheduled_at')
            ->get();

        return view('patients.show', compact('patient', 'appointments'));
    }

    public function create(): View
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        $patient = $this->appointmentService->createPatient($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', __('app.patients.success_created'));
    }

    public function edit(Patient $patient): View
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        $this->appointmentService->updatePatient($patient, $request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', __('app.patients.success_updated'));
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        $this->appointmentService->deletePatient($patient);

        return redirect()
            ->route('patients.index')
            ->with('success', __('app.patients.success_deleted'));
    }
}
