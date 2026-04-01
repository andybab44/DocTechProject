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
                    Calendar
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 font-medium' : '' }}">
                    Users
                </a>
                @endif
            </div>
            <div class="flex items-center gap-4">
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
                        Logout
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

</body>
</html>
