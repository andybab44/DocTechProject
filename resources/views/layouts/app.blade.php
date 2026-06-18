<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 antialiased text-stone-800">

    @auth
    <nav class="bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-6">
                {{-- App name with teal accent --}}
                <a href="{{ route('dashboard.' . auth()->user()->role->value) }}"
                   class="flex items-center gap-2 font-semibold text-stone-900 hover:text-teal-700 transition">
                    <span class="inline-block w-1.5 h-5 rounded-full bg-teal-600"></span>
                    {{ config('app.name') }}
                </a>
                <a href="{{ route('work-jobs.calendar') }}"
                   class="text-sm transition {{ request()->routeIs('work-jobs.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.calendar') }}
                </a>
                @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                <a href="{{ route('cases.index') }}"
                   class="text-sm transition {{ request()->routeIs('cases.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    Cases
                </a>
                @endif
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm transition {{ request()->routeIs('admin.users.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.users') }}
                </a>
                <a href="{{ route('admin.licenses.index') }}"
                   class="text-sm transition {{ request()->routeIs('admin.licenses.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.licenses') }}
                </a>
                <a href="{{ route('admin.vendors.index') }}"
                   class="text-sm transition {{ request()->routeIs('admin.vendors.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.vendors') }}
                </a>
                @endif
                @php $license = auth()->user()->license; @endphp
                @if($license && $license->isValid() && $license->hasModule(\App\Enums\Module::Inventory))
                <a href="{{ route('inventory.index') }}"
                   class="text-sm transition {{ request()->routeIs('inventory.*') || request()->routeIs('admin.inventory.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.inventory') }}
                </a>
                @endif
                @if($license && $license->isValid() && $license->hasModule(\App\Enums\Module::Appointments))
                <a href="{{ route('appointments.calendar') }}"
                   class="text-sm transition {{ request()->routeIs('appointments.*') || request()->routeIs('patients.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.appointments') }}
                </a>
                @endif
                @if(auth()->user()->isAdmin() && $license && $license->isValid() && $license->hasModule(\App\Enums\Module::Reviews))
                <a href="{{ route('admin.reviews.index') }}"
                   class="text-sm transition {{ request()->routeIs('admin.reviews.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.reviews') }}
                </a>
                @endif
                @if(auth()->user()->isAdmin() && $license && $license->isValid() && $license->hasModule(\App\Enums\Module::Analytics))
                <a href="{{ route('admin.analytics.index') }}"
                   class="text-sm transition {{ request()->routeIs('admin.analytics.*') ? 'text-teal-700 font-semibold' : 'text-stone-500 hover:text-teal-700' }}">
                    {{ __('app.nav.analytics') }}
                </a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                {{-- Language switcher --}}
                <div class="flex items-center gap-1 text-xs">
                    <a href="{{ route('locale.set', 'en') }}"
                       class="{{ app()->getLocale() === 'en' ? 'font-bold text-teal-700' : 'text-stone-400 hover:text-stone-600' }} transition">EN</a>
                    <span class="text-stone-300">|</span>
                    <a href="{{ route('locale.set', 'ro') }}"
                       class="{{ app()->getLocale() === 'ro' ? 'font-bold text-teal-700' : 'text-stone-400 hover:text-stone-600' }} transition">RO</a>
                </div>

                {{-- Notification bell --}}
                @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('notifications.index') }}"
                   class="relative text-stone-400 hover:text-teal-700 transition"
                   title="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                    @endif
                </a>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-stone-700 font-medium">{{ auth()->user()->name }}</span>
                    <span class="inline-block text-xs bg-teal-100 text-teal-800 rounded-full px-2 py-0.5 font-medium">
                        {{ auth()->user()->role->label() }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm text-stone-400 hover:text-stone-700 transition">
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
