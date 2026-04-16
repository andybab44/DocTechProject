@extends('layouts.app')

@section('title', __('app.dashboard.admin'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.dashboard.admin') }}</h1>
    <p class="text-gray-500 mt-1">{{ __('app.dashboard.welcome', ['name' => auth()->user()->name]) }} {{ __('app.dashboard.access_full') }}</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.role') }}</p>
        <p class="mt-1 text-2xl font-bold text-indigo-600">{{ auth()->user()->role->label() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.access_level') }}</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ __('app.dashboard.full') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ __('app.dashboard.status') }}</p>
        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ __('app.dashboard.active') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('admin.users.index') }}"
        class="bg-white rounded-lg shadow p-6 hover:shadow-md transition flex items-center gap-4 group">
        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center
                    group-hover:bg-indigo-200 transition text-indigo-600 font-bold text-lg">
            👤
        </div>
        <div>
            <p class="font-semibold text-gray-800">{{ __('app.dashboard.manage_users') }}</p>
            <p class="text-sm text-gray-500">{{ __('app.dashboard.manage_users_desc') }}</p>
        </div>
    </a>
</div>
@endsection
