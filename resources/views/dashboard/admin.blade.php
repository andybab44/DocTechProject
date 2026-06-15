@extends('layouts.app')

@section('title', __('app.dashboard.admin'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.admin') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }} {{ __('app.dashboard.access_full') }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-teal-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.role') }}</p>
        <p class="mt-1 text-2xl font-bold text-teal-700">{{ auth()->user()->role->label() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-green-500">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.access_level') }}</p>
        <p class="mt-1 text-2xl font-bold text-green-700">{{ __('app.dashboard.full') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 border-l-4 border-l-amber-400">
        <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.dashboard.status') }}</p>
        <p class="mt-1 text-2xl font-bold text-amber-600">{{ __('app.dashboard.active') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('admin.users.index') }}"
        class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 hover:shadow-md transition flex items-center gap-4 group">
        <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center
                    group-hover:bg-teal-200 transition text-teal-700 font-bold text-lg">
            👤
        </div>
        <div>
            <p class="font-semibold text-stone-800">{{ __('app.dashboard.manage_users') }}</p>
            <p class="text-sm text-stone-500">{{ __('app.dashboard.manage_users_desc') }}</p>
        </div>
    </a>
</div>
@endsection
