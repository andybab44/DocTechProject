@extends('layouts.app')

@section('title', 'New Work Job')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('work-jobs.calendar') }}" class="text-sm text-indigo-600 hover:underline">← Back to Calendar</a>
    <h1 class="text-2xl font-semibold text-gray-800">New Work Job</h1>
</div>

<div class="max-w-xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('work-jobs.store') }}" class="space-y-5">
        @csrf

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('title') border-red-400 @enderror">
            @error('title')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Scheduled at --}}
        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Date & Time <span class="text-red-500">*</span></label>
            @php
                $defaultDate = old('scheduled_at', $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d\TH:i') : '');
            @endphp
            <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ $defaultDate }}" required
                   min="{{ now()->format('Y-m-d\TH:i') }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('scheduled_at') border-red-400 @enderror">
            @error('scheduled_at')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Technician --}}
        <div>
            <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">Assign Technician <span class="text-red-500">*</span></label>
            <select id="technician_id" name="technician_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('technician_id') border-red-400 @enderror">
                <option value="">— Select a technician —</option>
                @foreach($technicians as $technician)
                <option value="{{ $technician->id }}" {{ old('technician_id') == $technician->id ? 'selected' : '' }}>
                    {{ $technician->name }}
                </option>
                @endforeach
            </select>
            @error('technician_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow">
                Create Job
            </button>
            <a href="{{ route('work-jobs.calendar') }}"
               class="px-5 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
