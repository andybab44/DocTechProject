<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 antialiased">

    @auth
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span class="font-semibold text-gray-800">{{ config('app.name') }}</span>
                <a href="{{ route('work-jobs.calendar') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('work-jobs.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.calendar') }}
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.users') }}
                </a>
                <a href="{{ route('admin.licenses.index') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('admin.licenses.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.licenses') }}
                </a>
                @endif
                @php $license = auth()->user()->license; @endphp
                @if($license && $license->isValid() && $license->hasModule(\App\Enums\Module::Inventory))
                <a href="{{ route('inventory.index') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('inventory.*') || request()->routeIs('admin.inventory.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.inventory') }}
                </a>
                @endif
                @if($license && $license->isValid() && $license->hasModule(\App\Enums\Module::Appointments))
                <a href="{{ route('appointments.calendar') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('appointments.*') || request()->routeIs('patients.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.appointments') }}
                </a>
                @endif
                @if(auth()->user()->isAdmin() && $license && $license->isValid() && $license->hasModule(\App\Enums\Module::Reviews))
                <a href="{{ route('admin.reviews.index') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('admin.reviews.*') ? 'text-indigo-600 font-medium' : '' }}">
                    {{ __('app.nav.reviews') }}
                </a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                {{-- Language switcher --}}
                <div class="flex items-center gap-1 text-xs">
                    <a href="{{ route('locale.set', 'en') }}"
                       class="{{ app()->getLocale() === 'en' ? 'font-bold text-indigo-600' : 'text-gray-400 hover:text-gray-600' }} transition">EN</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('locale.set', 'ro') }}"
                       class="{{ app()->getLocale() === 'ro' ? 'font-bold text-indigo-600' : 'text-gray-400 hover:text-gray-600' }} transition">RO</a>
                </div>
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    <span class="ml-1 inline-block text-xs bg-indigo-100 text-indigo-700 rounded-full px-2 py-0.5">
                        {{ auth()->user()->role->label() }}
                    </span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm text-gray-500 hover:text-gray-700 transition">
                        {{ __('app.nav.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
