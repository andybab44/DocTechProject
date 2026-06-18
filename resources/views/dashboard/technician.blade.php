@extends('layouts.app')

@section('title', __('app.dashboard.technician'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.technician') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>
</div>

{{-- My work job stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-orange-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.my_assigned_jobs') }}</p>
        <p class="mt-1 text-3xl font-bold text-orange-600">{{ $workJobStats['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-blue-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ \App\Enums\WorkJobStatus::InProgress->label() }}</p>
        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $workJobStats['by_status']->get('in_progress')?->total ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-green-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.delivered_this_month') }}</p>
        <p class="mt-1 text-3xl font-bold text-green-600">{{ $workJobStats['completed_this_month'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-purple-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ \App\Enums\WorkJobStatus::InReview->label() }}</p>
        <p class="mt-1 text-3xl font-bold text-purple-600">{{ $workJobStats['by_status']->get('in_review')?->total ?? 0 }}</p>
    </div>
</div>

{{-- Status breakdown --}}
@if ($workJobStats['total'] > 0)
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 mb-8">
    <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-4">{{ __('app.dashboard.my_jobs_breakdown') }}</p>
    @include('partials.work-job-status-bars', ['gridClass' => 'grid-cols-1 sm:grid-cols-2', 'showEmpty' => false])
</div>
@endif

<div class="mt-4">
    <a href="{{ route('work-jobs.calendar') }}"
       class="inline-flex items-center px-5 py-2.5 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition shadow">
        {{ __('app.dashboard.view_my_calendar') }}
    </a>
</div>
@endsection
