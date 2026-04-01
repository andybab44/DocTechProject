@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-16">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-8">

        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
            Sign in to {{ config('app.name') }}
        </h1>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                           focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                           @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                           focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                           @error('password') border-red-400 @enderror"
                >
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="mb-6 flex items-center">
                <input id="remember" type="checkbox" name="remember"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium
                       rounded-md py-2 text-sm transition">
                Sign in
            </button>
        </form>

    </div>
</div>
@endsection
