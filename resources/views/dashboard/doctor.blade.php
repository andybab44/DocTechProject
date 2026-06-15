@extends('layouts.app')

@section('title', __('app.dashboard.doctor'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.doctor') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.dashboard.welcome_dr', ['name' => auth()->user()->name]) }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-teal-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.role') }}</p>
        <p class="mt-1 text-2xl font-bold text-teal-700">{{ auth()->user()->role->label() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-green-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.access_level') }}</p>
        <p class="mt-1 text-2xl font-bold text-green-700">{{ __('app.dashboard.clinical') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-amber-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.status') }}</p>
        <p class="mt-1 text-2xl font-bold text-amber-600">{{ __('app.dashboard.active') }}</p>
    </div>
</div>

<div class="mt-8 flex gap-4">
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
