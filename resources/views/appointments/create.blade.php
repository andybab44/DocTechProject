@extends('layouts.app')

@section('title', __('app.appointments.create_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('appointments.calendar') }}" class="text-sm text-gray-500 hover:underline">← {{ __('app.appointments.back_calendar') }}</a>
    <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ __('app.appointments.create_title') }}</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_patient') }} *</label>
            <select id="patient_id" name="patient_id" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('patient_id') border-red-400 @enderror">
                <option value="">{{ __('app.appointments.select_patient') }}</option>
                @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>
                    {{ $patient->name }}
                </option>
                @endforeach
            </select>
            @error('patient_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        @if(auth()->user()->isAdmin() && $doctors->count() > 1)
        <div>
            <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_doctor') }} *</label>
            <select id="doctor_id" name="doctor_id"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('doctor_id') border-red-400 @enderror">
                <option value="">{{ __('app.appointments.select_doctor') }}</option>
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->name }}
                </option>
                @endforeach
            </select>
            @error('doctor_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        @endif

        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_scheduled_at') }} *</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                   value="{{ old('scheduled_at', $date ? $date . 'T09:00' : '') }}" required
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('scheduled_at') border-red-400 @enderror">
            @error('scheduled_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_notes') }}</label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('notes') border-red-400 @enderror">{{ old('notes') }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.appointments.btn_save') }}
            </button>
            <a href="{{ route('appointments.calendar') }}" class="text-sm text-gray-500 hover:underline">{{ __('app.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
