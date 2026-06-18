@extends('layouts.app')

@section('title', __('app.analytics.title'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.analytics.title') }}</h1>
    <p class="text-stone-500 mt-1">{{ __('app.analytics.subtitle') }}</p>
</div>

{{-- ── Users ─────────────────────────────────────────────────────────────── --}}
<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">{{ __('app.analytics.users') }}</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 border-l-teal-500">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.analytics.total_users') }}</p>
            <p class="mt-1 text-3xl font-bold text-teal-700">{{ $userStats['total'] }}</p>
        </div>
        @php
            $roleCards = [
                'admin'      => ['label' => 'Admins',      'border' => 'border-l-teal-400',   'text' => 'text-teal-600'],
                'doctor'     => ['label' => 'Doctors',     'border' => 'border-l-blue-400',   'text' => 'text-blue-600'],
                'technician' => ['label' => 'Technicians', 'border' => 'border-l-orange-400', 'text' => 'text-orange-600'],
            ];
        @endphp
        @foreach ($roleCards as $role => $meta)
        @php $count = $userStats['by_role']->get($role)?->total ?? 0; @endphp
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 {{ $meta['border'] }}">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ $meta['label'] }}</p>
            <p class="mt-1 text-3xl font-bold {{ $meta['text'] }}">{{ $count }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ── Work Jobs ─────────────────────────────────────────────────────────── --}}
<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">{{ __('app.analytics.work_jobs') }}</h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Total + status breakdown --}}
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6 col-span-1">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-1">{{ __('app.analytics.total_jobs') }}</p>
            <p class="text-4xl font-bold text-stone-800 mb-4">{{ $workJobStats['total'] }}</p>
            @include('partials.work-job-status-bars', ['gridClass' => 'grid-cols-1'])
        </div>

        {{-- Jobs by doctor --}}
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6 col-span-1">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-4">{{ __('app.analytics.jobs_by_doctor') }}</p>
            @forelse ($workJobsByDoctor as $row)
            @php $pct = round(($row->total / $total) * 100); @endphp
            <div class="mb-3">
                <div class="flex justify-between text-xs text-stone-600 mb-0.5">
                    <span class="truncate max-w-[160px]">{{ $row->doctor?->name ?? '—' }}</span>
                    <span class="font-medium">{{ $row->total }}</span>
                </div>
                <div class="w-full bg-stone-100 rounded-full h-2">
                    <div class="bg-blue-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-stone-400">{{ __('app.analytics.no_data') }}</p>
            @endforelse
        </div>

        {{-- Jobs by technician --}}
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6 col-span-1">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium mb-4">{{ __('app.analytics.jobs_by_technician') }}</p>
            @forelse ($workJobsByTechnician as $row)
            @php $pct = round(($row->total / $total) * 100); @endphp
            <div class="mb-3">
                <div class="flex justify-between text-xs text-stone-600 mb-0.5">
                    <span class="truncate max-w-[160px]">{{ $row->technician?->name ?? '—' }}</span>
                    <span class="font-medium">{{ $row->total }}</span>
                </div>
                <div class="w-full bg-stone-100 rounded-full h-2">
                    <div class="bg-orange-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-stone-400">{{ __('app.analytics.no_data') }}</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ── Appointments (module-gated) ──────────────────────────────────────── --}}
@if ($hasAppointments && $appointmentStats !== null)
<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">{{ __('app.analytics.appointments') }}</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 border-l-blue-500">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.analytics.total_appointments') }}</p>
            <p class="mt-1 text-3xl font-bold text-blue-700">{{ $appointmentStats['total'] }}</p>
        </div>
        @php
            $apptCards = [
                'scheduled' => ['border' => 'border-l-blue-400',  'text' => 'text-blue-600'],
                'completed' => ['border' => 'border-l-green-400', 'text' => 'text-green-600'],
                'cancelled' => ['border' => 'border-l-red-400',   'text' => 'text-red-600'],
            ];
        @endphp
        @foreach (\App\Enums\AppointmentStatus::cases() as $status)
        @php
            $count = $appointmentStats['by_status']->get($status->value)?->total ?? 0;
            $card  = $apptCards[$status->value] ?? ['border' => 'border-l-stone-400', 'text' => 'text-stone-600'];
        @endphp
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 {{ $card['border'] }}">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ $status->label() }}</p>
            <p class="mt-1 text-3xl font-bold {{ $card['text'] }}">{{ $count }}</p>
        </div>
        @endforeach
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 border-l-stone-400">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.analytics.total_patients') }}</p>
            <p class="mt-1 text-3xl font-bold text-stone-700">{{ $patientCount }}</p>
        </div>
    </div>
</section>
@endif

{{-- ── Inventory (module-gated) ─────────────────────────────────────────── --}}
@if ($hasInventory && $inventoryStats !== null)
<section class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">{{ __('app.analytics.inventory') }}</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 border-l-teal-400">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.analytics.total_items') }}</p>
            <p class="mt-1 text-3xl font-bold text-teal-700">{{ $inventoryStats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 border-l-4 border-l-red-400">
            <p class="text-xs text-stone-500 uppercase tracking-wider font-medium">{{ __('app.analytics.low_stock') }}</p>
            <p class="mt-1 text-3xl font-bold {{ $inventoryStats['low_stock'] > 0 ? 'text-red-600' : 'text-stone-600' }}">
                {{ $inventoryStats['low_stock'] }}
            </p>
        </div>
    </div>
</section>
@endif

@endsection
