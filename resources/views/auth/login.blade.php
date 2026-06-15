@extends('layouts.app')

@section('title', __('app.auth.sign_in_button'))

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-16">
    <div class="w-full max-w-sm">

        {{-- Brand header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 mb-3">
                <span class="inline-block w-2 h-7 rounded-full bg-teal-600"></span>
                <span class="text-2xl font-bold text-stone-900 tracking-tight">{{ config('app.name') }}</span>
            </div>
            <p class="text-sm text-stone-500">{{ __('app.auth.sign_in', ['app' => '']) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-8">

            @if(session('status'))
            <div class="mb-5 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-stone-700 mb-1">
                        {{ __('app.auth.email') }}
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full border border-stone-300 rounded-lg px-3 py-2.5 text-sm text-stone-800
                               placeholder-stone-400 bg-stone-50
                               focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-400/30
                               transition @error('email') border-red-400 bg-red-50 @enderror"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-stone-700 mb-1">
                        {{ __('app.auth.password') }}
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-stone-300 rounded-lg px-3 py-2.5 text-sm text-stone-800
                               bg-stone-50
                               focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-400/30
                               transition @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember"
                        class="h-4 w-4 accent-teal-600 border-stone-300 rounded">
                    <label for="remember" class="ml-2 text-sm text-stone-600">{{ __('app.auth.remember_me') }}</label>
                </div>

                <button type="submit"
                    class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold
                           rounded-lg py-2.5 text-sm transition shadow-sm">
                    {{ __('app.auth.sign_in_button') }}
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

