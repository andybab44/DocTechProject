@extends('layouts.app')

@section('title', __('app.dashboard.doctor'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.dashboard.doctor') }}</h1>
    <p class="text-gray-500 mt-1">{{ __('app.dashboard.welcome_dr', ['name' => auth()->user()->name]) }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.role') }}</p>
        <p class="mt-1 text-2xl font-bold text-blue-600">{{ auth()->user()->role->label() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.access_level') }}</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ __('app.dashboard.clinical') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.status') }}</p>
        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ __('app.dashboard.active') }}</p>
    </div>
</div>

<div class="mt-8 flex gap-4">
    <a href="{{ route('work-jobs.calendar') }}"
       class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow">
        {{ __('app.dashboard.view_calendar') }}
    </a>
    <a href="{{ route('work-jobs.create') }}"
       class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow">
        {{ __('app.dashboard.new_work_job') }}
    </a>
</div>
@endsection
