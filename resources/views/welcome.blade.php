<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 antialiased text-stone-800 flex items-center justify-center p-8">
    <div class="text-center max-w-sm">
        <div class="inline-flex items-center gap-2 mb-6">
            <span class="inline-block w-2 h-8 rounded-full bg-teal-600"></span>
            <span class="text-3xl font-bold text-stone-900 tracking-tight">{{ config('app.name') }}</span>
        </div>
        <p class="text-stone-500 text-sm mb-8 leading-relaxed">
            Connecting stomatologists and dental technicians.<br>Clear cases, clear status.
        </p>
        <a href="{{ route('login') }}"
           class="inline-flex items-center px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            Sign in
        </a>
    </div>
</body>
</html>
