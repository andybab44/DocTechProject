@extends('layouts.app')

@section('title', $patient->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('patients.index') }}" class="text-sm text-gray-500 hover:underline">← {{ __('app.patients.back') }}</a>
        <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ $patient->name }}</h1>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('patients.edit', $patient) }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
              text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.patients.btn_edit') }}
    </a>
    @endif
</div>

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Patient details --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-base font-semibold text-gray-700">{{ __('app.patients.details') }}</h2>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="py-2 grid grid-cols-2">
                <dt class="text-gray-500">{{ __('app.patients.col_dob') }}</dt>
                <dd class="text-gray-800">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</dd>
            </div>
            <div class="py-2 grid grid-cols-2">
                <dt class="text-gray-500">{{ __('app.patients.col_email') }}</dt>
                <dd class="text-gray-800">{{ $patient->email ?? '—' }}</dd>
            </div>
            <div class="py-2 grid grid-cols-2">
                <dt class="text-gray-500">{{ __('app.patients.col_phone') }}</dt>
                <dd class="text-gray-800">{{ $patient->phone ?? '—' }}</dd>
            </div>
        </dl>
        @if($patient->notes)
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('app.patients.notes') }}</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $patient->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Appointment history --}}
    <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-700">{{ __('app.patients.appointment_history') }}</h2>
            @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
               class="text-sm text-indigo-600 hover:underline">{{ __('app.patients.new_appointment') }}</a>
            @endif
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.appointments.col_date') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.appointments.col_doctor') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.appointments.col_status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($appointments as $appointment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-800">{{ $appointment->scheduled_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $appointment->doctor->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 {{ $appointment->status->colour() }}">
                            {{ $appointment->status->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('appointments.show', $appointment) }}" class="text-sm text-indigo-600 hover:underline">{{ __('app.appointments.btn_view') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">{{ __('app.patients.no_appointments') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
