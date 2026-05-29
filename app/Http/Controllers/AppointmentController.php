<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService) {}

    public function calendar(Request $request): View
    {
        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        $currentMonth = Carbon::create($year, $month, 1);
        $prevMonth    = $currentMonth->copy()->subMonth();
        $nextMonth    = $currentMonth->copy()->addMonth();

        $appointments = $this->appointmentService->getForCalendar(auth()->user(), $year, $month);

        $appointmentsByDay = $appointments->groupBy(
            fn (Appointment $a) => $a->scheduled_at->day
        );

        return view('appointments.calendar', compact(
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'appointmentsByDay',
        ));
    }

    public function show(Appointment $appointment): View
    {
        $this->authorizeAccess($appointment);

        $appointment->load(['patient', 'doctor', 'workJob']);

        return view('appointments.show', compact('appointment'));
    }

    public function create(Request $request): View
    {
        abort_if(auth()->user()->isTechnician(), 403);

        $patients = Patient::orderBy('name')->get();
        $doctors  = auth()->user()->isAdmin()
            ? User::where('role', 'doctor')->orderBy('name')->get()
            : collect([auth()->user()]);
        $date = $request->query('date');

        return view('appointments.create', compact('patients', 'doctors', 'date'));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        abort_if(auth()->user()->isTechnician(), 403);

        $appointment = $this->appointmentService->create(auth()->user(), $request->validated());

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', __('app.appointments.success_created'));
    }

    public function edit(Appointment $appointment): View
    {
        $this->authorizeEdit($appointment);

        $appointment->load(['patient', 'doctor']);

        return view('appointments.edit', compact('appointment'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeEdit($appointment);

        $this->appointmentService->update($appointment, $request->validated());

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', __('app.appointments.success_updated'));
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $this->authorizeEdit($appointment);

        abort_if(
            $appointment->status !== AppointmentStatus::Scheduled,
            422,
            __('app.appointments.error_not_scheduled')
        );

        $this->appointmentService->cancel($appointment);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', __('app.appointments.success_cancelled'));
    }

    public function complete(Appointment $appointment): RedirectResponse
    {
        $this->authorizeEdit($appointment);

        abort_if(
            $appointment->status !== AppointmentStatus::Scheduled,
            422,
            __('app.appointments.error_not_scheduled')
        );

        $this->appointmentService->complete($appointment);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', __('app.appointments.success_completed'));
    }

    private function authorizeAccess(Appointment $appointment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        abort_if($appointment->doctor_id !== $user->id, 403);
    }

    private function authorizeEdit(Appointment $appointment): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        // Doctors can only edit their own appointments
        abort_if(! $user->isDoctor() || $appointment->doctor_id !== $user->id, 403);
    }
}
