@extends('layouts.app')

@section('title', __('app.dashboard.technician'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.dashboard.technician') }}</h1>
    <p class="text-gray-500 mt-1">{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.role') }}</p>
        <p class="mt-1 text-2xl font-bold text-orange-600">{{ auth()->user()->role->label() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.access_level') }}</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ __('app.dashboard.technical') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.status') }}</p>
        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ __('app.dashboard.active') }}</p>
    </div>
</div>

<div class="mt-8">
    <a href="{{ route('work-jobs.calendar') }}"
       class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow">
        {{ __('app.dashboard.view_my_calendar') }}
    </a>
</div>
@endsection
