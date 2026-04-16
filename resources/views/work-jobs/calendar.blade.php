@extends('layouts.app')

@section('title', __('app.calendar.title', ['month' => $currentMonth->format('F Y')]))

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-2xl font-semibold text-gray-800">
        {{ __('app.calendar.title', ['month' => $currentMonth->format('F Y')]) }}
    </h1>
    <div class="flex items-center gap-3">
        {{-- Month navigation --}}
        <a href="{{ route('work-jobs.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
           class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
            ← {{ $prevMonth->format('M') }}
        </a>
        <a href="{{ route('work-jobs.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
           class="inline-flex items-center px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
            {{ $nextMonth->format('M') }} →
        </a>
        @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
        <a href="{{ route('work-jobs.create') }}"
           class="inline-flex items-center px-4 py-1.5 text-sm bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 transition">
            {{ __('app.calendar.new_job') }}
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

{{-- Calendar grid --}}
@php
    $daysInMonth  = $currentMonth->daysInMonth;
    $firstDayOfWeek = $currentMonth->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon … 7=Sun
    $today = \Illuminate\Support\Carbon::today();
    $dayHeaders = [
        __('app.calendar.mon'), __('app.calendar.tue'), __('app.calendar.wed'),
        __('app.calendar.thu'), __('app.calendar.fri'), __('app.calendar.sat'), __('app.calendar.sun'),
    ];
@endphp

<div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
    {{-- Day-of-week headers --}}
    <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));" class="border-b border-gray-200 bg-gray-50">
        @foreach($dayHeaders as $d)
        <div class="py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200 last:border-r-0">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Calendar cells --}}
    @php
        $totalCells = $firstDayOfWeek - 1 + $daysInMonth;
        $trailingCells = (7 - ($totalCells % 7)) % 7;
    @endphp
    <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
        {{-- Leading empty cells --}}
        @for($i = 1; $i < $firstDayOfWeek; $i++)
        <div style="min-height:96px;" class="bg-gray-50 p-1 border-r border-b border-gray-100"></div>
        @endfor

        {{-- Day cells --}}
        @for($day = 1; $day <= $daysInMonth; $day++)
        @php
            $date    = $currentMonth->copy()->day($day);
            $isToday = $date->isSameDay($today);
            $dayJobs = $jobsByDay->get($day, collect());
            $col     = (($firstDayOfWeek - 1 + $day - 1) % 7) + 1; // 1–7
            $isLastCol = $col === 7;
        @endphp
        <div style="min-height:96px;" class="p-1 border-b border-gray-100 {{ $isLastCol ? '' : 'border-r' }} border-gray-100 {{ $isToday ? 'bg-indigo-50' : 'bg-white' }}">
            <div class="flex items-center justify-between mb-1">
                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-semibold rounded-full
                    {{ $isToday ? 'bg-indigo-600 text-white' : 'text-gray-400' }}">
                    {{ $day }}
                </span>
                @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                <a href="{{ route('work-jobs.create', ['date' => $date->format('Y-m-d')]) }}"
                   class="text-gray-300 hover:text-indigo-500 text-base leading-none transition" title="{{ __('app.calendar.add_job') }}">+</a>
                @endif
            </div>

            @foreach($dayJobs as $job)
            @php $color = $job->status->color(); @endphp
            <a href="{{ route('work-jobs.show', $job) }}"
               title="{{ $job->title }} — {{ $job->scheduled_at->format('H:i') }}"
               style="display:block;margin-bottom:2px;padding:1px 6px;border-radius:4px;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               class="job-pill job-{{ $color }}">
                {{ $job->scheduled_at->format('H:i') }} {{ $job->title }}
            </a>
            @endforeach
        </div>
        @endfor

        {{-- Trailing empty cells --}}
        @for($i = 0; $i < $trailingCells; $i++)
        <div style="min-height:96px;" class="bg-gray-50 p-1 border-r border-b border-gray-100 last:border-r-0"></div>
        @endfor
    </div>
</div>

{{-- Legend --}}
<div class="mt-4 flex flex-wrap gap-3">
    @foreach(\App\Enums\WorkJobStatus::cases() as $status)
    @php $c = $status->color(); @endphp
    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
        <span class="legend-{{ $c }}" style="display:inline-block;width:12px;height:12px;border-radius:50%;"></span>
        {{ $status->label() }}
    </span>
    @endforeach
</div>
@endsection
