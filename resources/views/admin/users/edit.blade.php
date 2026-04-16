@extends('layouts.app')

@section('title', __('app.users.edit_title') . ' – ' . $user->name)

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 transition">{{ __('app.users.btn_back') }}</a>
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.users.edit_title') }}</h1>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-lg bg-white rounded-lg shadow p-8">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.users.field_name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('name') border-red-400 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.users.field_email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('email') border-red-400 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.users.field_role') }}</label>
            <select id="role" name="role" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('role') border-red-400 @enderror">
                @foreach (\App\Enums\Role::cases() as $role)
                    <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                        {{ $role->label() }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password (optional) --}}
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('app.users.field_password_new') }} <span class="text-gray-400 font-normal">{{ __('app.users.field_password_keep') }}</span>
            </label>
            <input id="password" type="password" name="password" autocomplete="new-password"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('password') border-red-400 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.users.field_password_new_confirm') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">
                {{ __('app.users.btn_save') }}
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                {{ __('app.users.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
