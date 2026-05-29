@extends('layouts.app')

@section('title', __('app.appointments.edit_title', ['patient' => $appointment->patient->name]))

@section('content')
<div class="mb-6">
    <a href="{{ route('appointments.show', $appointment) }}" class="text-sm text-gray-500 hover:underline">← {{ $appointment->patient->name }}</a>
    <h1 class="text-2xl font-semibold text-gray-800 mt-1">
        {{ __('app.appointments.edit_title', ['patient' => $appointment->patient->name]) }}
    </h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_patient') }}</label>
            <p class="text-sm text-gray-800 py-2">{{ $appointment->patient->name }}</p>
        </div>

        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_scheduled_at') }} *</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                   value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}" required
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('scheduled_at') border-red-400 @enderror">
            @error('scheduled_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.appointments.field_notes') }}</label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('notes') border-red-400 @enderror">{{ old('notes', $appointment->notes) }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.appointments.btn_save') }}
            </button>
            <a href="{{ route('appointments.show', $appointment) }}" class="text-sm text-gray-500 hover:underline">{{ __('app.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
