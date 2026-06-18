@extends('layouts.app')

@section('title', __('app.dashboard.doctor'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.doctor') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.dashboard.welcome_dr', ['name' => auth()->user()->name]) }}</p>
</div>

{{-- My work job stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-teal-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.my_total_jobs') }}</p>
        <p class="mt-1 text-3xl font-bold text-teal-700">{{ $workJobStats['total'] }}</p>
    </div>
    @php
        $highlight = [
            'in_progress'   => ['colour' => 'blue',   'label' => \App\Enums\WorkJobStatus::InProgress->label()],
            'in_review'     => ['colour' => 'purple', 'label' => \App\Enums\WorkJobStatus::InReview->label()],
            'delivered'     => ['colour' => 'green',  'label' => \App\Enums\WorkJobStatus::Delivered->label()],
        ];
    @endphp
    @foreach ($highlight as $statusVal => $meta)
    @php $count = $workJobStats['by_status']->get($statusVal)?->total ?? 0; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-{{ $meta['colour'] }}-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ $meta['label'] }}</p>
        <p class="mt-1 text-3xl font-bold text-{{ $meta['colour'] }}-600">{{ $count }}</p>
    </div>
    @endforeach
</div>

{{-- Status breakdown bar --}}
@if ($workJobStats['total'] > 0)
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 mb-8">
    <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-4">{{ __('app.dashboard.my_jobs_breakdown') }}</p>
    @include('partials.work-job-status-bars', ['gridClass' => 'grid-cols-1 sm:grid-cols-2', 'showEmpty' => false])
</div>
@endif

{{-- Next appointment (if module active) --}}
@if ($nextAppointment)
<div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-8">
    <p class="text-xs text-blue-600 uppercase tracking-wider font-medium mb-1">{{ __('app.dashboard.next_appointment') }}</p>
    <p class="font-semibold text-blue-800">{{ $nextAppointment->patient->name }}</p>
    <p class="text-sm text-blue-600">{{ $nextAppointment->scheduled_at->format('D, d M Y — H:i') }}</p>
</div>
@endif

{{-- Action buttons --}}
<div class="flex flex-wrap gap-3">
    <a href="{{ route('work-jobs.calendar') }}"
       class="inline-flex items-center px-5 py-2.5 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition shadow">
        {{ __('app.dashboard.view_calendar') }}
    </a>
    <a href="{{ route('work-jobs.create') }}"
       class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-300 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-50 transition shadow">
        {{ __('app.dashboard.new_work_job') }}
    </a>
    <a href="{{ route('cases.index') }}"
       class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-300 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-50 transition shadow">
        View Cases
    </a>
</div>
@endsection
