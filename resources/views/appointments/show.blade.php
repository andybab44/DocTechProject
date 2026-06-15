@extends('layouts.app')

@section('title', __('app.appointments.show_title', ['patient' => $appointment->patient->name]))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('appointments.calendar') }}" class="text-sm text-stone-500 hover:underline">← {{ __('app.appointments.back_calendar') }}</a>
        <h1 class="text-2xl font-semibold text-stone-800 mt-1">
            {{ __('app.appointments.show_title', ['patient' => $appointment->patient->name]) }}
        </h1>
    </div>
    <div class="flex items-center gap-3">
        @if($appointment->status === \App\Enums\AppointmentStatus::Scheduled)
            @if(auth()->user()->isAdmin() || auth()->id() === $appointment->doctor_id)
            <a href="{{ route('appointments.edit', $appointment) }}"
               class="inline-flex items-center text-sm bg-white border border-stone-300 rounded-md px-4 py-2 hover:bg-stone-50 transition">
                {{ __('app.appointments.btn_edit') }}
            </a>
            <form method="POST" action="{{ route('appointments.complete', $appointment) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center text-sm bg-green-600 hover:bg-green-700 text-white rounded-md px-4 py-2 transition">
                    {{ __('app.appointments.btn_complete') }}
                </button>
            </form>
            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}"
                  onsubmit="return confirm('{{ __('app.appointments.cancel_confirm') }}')">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center text-sm bg-red-600 hover:bg-red-700 text-white rounded-md px-4 py-2 transition">
                    {{ __('app.appointments.btn_cancel') }}
                </button>
            </form>
            @endif
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-2xl space-y-4">
    <dl class="divide-y divide-stone-100 text-sm">
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_patient') }}</dt>
            <dd class="col-span-2">
                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-teal-700 hover:underline">
                    {{ $appointment->patient->name }}
                </a>
            </dd>
        </div>
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_doctor') }}</dt>
            <dd class="col-span-2 text-stone-800">{{ $appointment->doctor->name }}</dd>
        </div>
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_date') }}</dt>
            <dd class="col-span-2 text-stone-800">{{ $appointment->scheduled_at->format('d M Y H:i') }}</dd>
        </div>
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_status') }}</dt>
            <dd class="col-span-2">
                <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 {{ $appointment->status->colour() }}">
                    {{ $appointment->status->label() }}
                </span>
            </dd>
        </div>
        @if($appointment->workJob)
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_work_job') }}</dt>
            <dd class="col-span-2">
                <a href="{{ route('work-jobs.show', $appointment->workJob) }}" class="text-teal-700 hover:underline">
                    {{ $appointment->workJob->title }}
                </a>
            </dd>
        </div>
        @endif
        @if($appointment->notes)
        <div class="py-3 grid grid-cols-3">
            <dt class="font-medium text-stone-500">{{ __('app.appointments.col_notes') }}</dt>
            <dd class="col-span-2 text-stone-700 whitespace-pre-wrap">{{ $appointment->notes }}</dd>
        </div>
        @endif
    </dl>
</div>
@endsection
