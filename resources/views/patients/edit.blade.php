@extends('layouts.app')

@section('title', __('app.patients.edit_title', ['name' => $patient->name]))

@section('content')
<div class="mb-6">
    <a href="{{ route('patients.show', $patient) }}" class="text-sm text-gray-500 hover:underline">← {{ $patient->name }}</a>
    <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ __('app.patients.edit_title', ['name' => $patient->name]) }}</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.field_name') }} *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}" required
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.field_dob') }}</label>
            <input type="date" id="date_of_birth" name="date_of_birth"
                   value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('date_of_birth') border-red-400 @enderror">
            @error('date_of_birth')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.field_email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $patient->email) }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.field_phone') }}</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('phone') border-red-400 @enderror">
            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.field_notes') }}</label>
            <textarea id="notes" name="notes" rows="4"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('notes') border-red-400 @enderror">{{ old('notes', $patient->notes) }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.patients.btn_save') }}
            </button>
            <a href="{{ route('patients.show', $patient) }}" class="text-sm text-gray-500 hover:underline">{{ __('app.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
