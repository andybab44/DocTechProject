@extends('layouts.app')

@section('title', $case->title)

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('cases.index') }}" class="text-sm text-teal-700 hover:underline">← Cases</a>
    <h1 class="text-2xl font-semibold text-stone-800">{{ $case->title }}</h1>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="max-w-3xl space-y-6">
    {{-- Case details --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 space-y-4">
        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
            <dt class="text-stone-500 font-medium">Patient</dt>
            <dd class="text-stone-800">{{ $case->patient->name }}</dd>
            <dt class="text-stone-500 font-medium">Doctor</dt>
            <dd class="text-stone-800">{{ $case->doctor->name }}</dd>
            <dt class="text-stone-500 font-medium">Created</dt>
            <dd class="text-stone-800">{{ $case->created_at->toFormattedDateString() }}</dd>
        </dl>

        @if($case->description)
        <div>
            <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Description</p>
            <p class="text-sm text-stone-700 whitespace-pre-wrap">{{ $case->description }}</p>
        </div>
        @endif

        @if($case->notes)
        <div>
            <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Internal Notes</p>
            <p class="text-sm text-stone-700 whitespace-pre-wrap">{{ $case->notes }}</p>
        </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <a href="{{ route('cases.edit', $case) }}"
               class="px-4 py-2 text-sm bg-white border border-stone-300 rounded-lg shadow-sm hover:bg-stone-50 transition">
                Edit Case
            </a>
            <form method="POST" action="{{ route('cases.destroy', $case) }}"
                  onsubmit="return confirm('Delete this case? This will not delete linked work jobs or appointments.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Work Jobs --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-stone-700">Work Jobs ({{ $case->workJobs->count() }})</h2>
            <a href="{{ route('work-jobs.create') }}"
               class="text-xs text-teal-700 hover:underline">+ New Job</a>
        </div>

        @if($case->workJobs->isEmpty())
        <p class="text-sm text-stone-400">No work jobs linked to this case.</p>
        @else
        <ul class="divide-y divide-stone-100">
            @foreach($case->workJobs as $job)
            @php $color = $job->status->color(); @endphp
            <li class="flex items-center justify-between py-3">
                <div>
                    <a href="{{ route('work-jobs.show', $job) }}"
                       class="text-sm font-medium text-stone-800 hover:text-teal-700">{{ $job->title }}</a>
                    <p class="text-xs text-stone-400 mt-0.5">
                        {{ $job->scheduled_at->format('d M Y, H:i') }}
                        · {{ $job->technician->name }}
                    </p>
                </div>
                <span class="job-pill job-{{ $color }}"
                      style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:600;">
                    {{ $job->status->label() }}
                </span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Appointments --}}
    @if(($license = auth()->user()->license) && $license->isValid() && $license->hasModule(\App\Enums\Module::Appointments))
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-stone-700">Appointments ({{ $case->appointments->count() }})</h2>
            <a href="{{ route('appointments.create') }}"
               class="text-xs text-teal-700 hover:underline">+ New Appointment</a>
        </div>

        @if($case->appointments->isEmpty())
        <p class="text-sm text-stone-400">No appointments linked to this case.</p>
        @else
        <ul class="divide-y divide-stone-100">
            @foreach($case->appointments as $appt)
            @php $apptColor = match($appt->status->value) {
                'scheduled' => 'blue',
                'completed' => 'green',
                'cancelled' => 'red',
                default     => 'gray',
            }; @endphp
            <li class="flex items-center justify-between py-3">
                <div>
                    <a href="{{ route('appointments.show', $appt) }}"
                       class="text-sm font-medium text-stone-800 hover:text-teal-700">
                        {{ $appt->scheduled_at->format('d M Y, H:i') }}
                    </a>
                    @if($appt->notes)
                    <p class="text-xs text-stone-400 mt-0.5 truncate max-w-xs">{{ $appt->notes }}</p>
                    @endif
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    bg-{{ $apptColor }}-100 text-{{ $apptColor }}-800">
                    {{ $appt->status->label() }}
                </span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
    @endif
</div>
@endsection
