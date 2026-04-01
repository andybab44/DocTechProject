@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        ← Back
    </a>
    <h1 class="text-2xl font-semibold text-gray-800">Add User</h1>
</div>

<div class="max-w-lg bg-white rounded-lg shadow p-8">
    <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
        @csrf

        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('name') border-red-400 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('email') border-red-400 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select id="role" name="role" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('role') border-red-400 @enderror">
                <option value="" disabled selected>Select a role…</option>
                @foreach (\App\Enums\Role::cases() as $role)
                    <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                        {{ $role->label() }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                       @error('password') border-red-400 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Confirmation --}}
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                       focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md
                       px-5 py-2 text-sm transition">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 transition">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
