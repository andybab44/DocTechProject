@extends('layouts.app')

@section('title', __('app.dashboard.admin'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.admin') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }} {{ __('app.dashboard.access_full') }}</p>
</div>

{{-- Quick-stat cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-teal-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.total_users') }}</p>
        <p class="mt-1 text-3xl font-bold text-teal-700">{{ $quickStats['users'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-blue-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.total_jobs') }}</p>
        <p class="mt-1 text-3xl font-bold text-blue-700">{{ $quickStats['work_jobs'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-amber-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.active_jobs') }}</p>
        <p class="mt-1 text-3xl font-bold text-amber-600">{{ $quickStats['active_jobs'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 border-l-4 border-l-stone-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.total_patients') }}</p>
        <p class="mt-1 text-3xl font-bold text-stone-700">{{ $quickStats['patients'] }}</p>
    </div>
</div>

{{-- Work job status breakdown --}}
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 mb-8">
    <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-4">{{ __('app.dashboard.jobs_by_status') }}</p>
    @include('partials.work-job-status-bars', ['gridClass' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'])
</div>

{{-- Navigation shortcuts --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('admin.users.index') }}"
        class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 hover:shadow-md transition flex items-center gap-4 group">
        <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center
                    group-hover:bg-teal-200 transition text-teal-700 font-bold text-lg"
             aria-hidden="true">
            👤
        </div>
        <div>
            <p class="font-semibold text-stone-800">{{ __('app.dashboard.manage_users') }}</p>
            <p class="text-sm text-stone-500">{{ __('app.dashboard.manage_users_desc') }}</p>
        </div>
    </a>
    <a href="{{ route('work-jobs.calendar') }}"
        class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 hover:shadow-md transition flex items-center gap-4 group">
        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center
                    group-hover:bg-blue-200 transition text-blue-700 font-bold text-lg"
             aria-hidden="true">
            📅
        </div>
        <div>
            <p class="font-semibold text-stone-800">{{ __('app.dashboard.view_calendar') }}</p>
            <p class="text-sm text-stone-500">{{ __('app.dashboard.view_calendar_desc') }}</p>
        </div>
    </a>
</div>
@endsection
