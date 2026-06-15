@extends('layouts.app')

@section('title', __('app.appointments.calendar_title', ['month' => $currentMonth->format('F Y')]))

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-2xl font-semibold text-stone-800">
        {{ __('app.appointments.calendar_title', ['month' => $currentMonth->format('F Y')]) }}
    </h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('appointments.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
           class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-stone-300 rounded-lg shadow-sm hover:bg-stone-50 transition">
            ← {{ $prevMonth->format('M') }}
        </a>
        <a href="{{ route('appointments.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
           class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-stone-300 rounded-lg shadow-sm hover:bg-stone-50 transition">
            {{ $nextMonth->format('M') }} →
        </a>
        @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
        <a href="{{ route('appointments.create') }}"
           class="inline-flex items-center px-4 py-1.5 text-sm bg-teal-700 text-white rounded-lg shadow-sm hover:bg-teal-800 transition">
            {{ __('app.appointments.new_appointment') }}
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

@php
    $daysInMonth    = $currentMonth->daysInMonth;
    $firstDayOfWeek = $currentMonth->copy()->startOfMonth()->dayOfWeekIso;
    $today          = \Illuminate\Support\Carbon::today();
    $dayHeaders = [
        __('app.calendar.mon'), __('app.calendar.tue'), __('app.calendar.wed'),
        __('app.calendar.thu'), __('app.calendar.fri'), __('app.calendar.sat'), __('app.calendar.sun'),
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden border border-stone-200">
    <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));" class="border-b border-stone-200 bg-stone-50">
        @foreach($dayHeaders as $d)
        <div class="py-2 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider border-r border-stone-200 last:border-r-0">{{ $d }}</div>
        @endforeach
    </div>

    @php
        $totalCells    = $firstDayOfWeek - 1 + $daysInMonth;
        $trailingCells = (7 - ($totalCells % 7)) % 7;
    @endphp
    <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
        @for($i = 1; $i < $firstDayOfWeek; $i++)
        <div style="min-height:96px;" class="bg-stone-50 p-1 border-r border-b border-stone-100"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
        @php
            $date         = $currentMonth->copy()->day($day);
            $isToday      = $date->isSameDay($today);
            $dayAppts     = $appointmentsByDay->get($day, collect());
            $col          = (($firstDayOfWeek - 1 + $day - 1) % 7) + 1;
            $isLastCol    = $col === 7;
        @endphp
        <div style="min-height:96px;" class="p-1 border-b border-stone-100 {{ $isLastCol ? '' : 'border-r' }} border-stone-100 {{ $isToday ? 'bg-teal-50' : 'bg-white' }}">
            <div class="flex items-center justify-between mb-1">
                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-semibold rounded-full
                    {{ $isToday ? 'bg-teal-700 text-white' : 'text-stone-400' }}">
                    {{ $day }}
                </span>
                @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                <a href="{{ route('appointments.create', ['date' => $date->format('Y-m-d')]) }}"
                   class="text-stone-300 hover:text-teal-600 text-base leading-none transition" title="{{ __('app.appointments.add_appointment') }}">+</a>
                @endif
            </div>

            @foreach($dayAppts as $appointment)
            <a href="{{ route('appointments.show', $appointment) }}"
               title="{{ $appointment->patient->name }} — {{ $appointment->scheduled_at->format('H:i') }}"
               style="display:block;margin-bottom:2px;padding:1px 6px;border-radius:4px;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               class="appt-pill {{ $appointment->status->colour() }}">
                {{ $appointment->scheduled_at->format('H:i') }} {{ $appointment->patient->name }}
            </a>
            @endforeach
        </div>
        @endfor

        @for($i = 0; $i < $trailingCells; $i++)
        <div style="min-height:96px;" class="bg-stone-50 p-1 border-r border-b border-stone-100 last:border-r-0"></div>
        @endfor
    </div>
</div>

{{-- Legend --}}
<div class="mt-4 flex flex-wrap gap-3">
    @foreach(\App\Enums\AppointmentStatus::cases() as $status)
    <span class="inline-flex items-center gap-1.5 text-xs text-stone-600">
        <span class="inline-block w-3 h-3 rounded-full {{ $status->colour() }}"></span>
        {{ $status->label() }}
    </span>
    @endforeach
</div>
@endsection
