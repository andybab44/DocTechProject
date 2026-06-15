@extends('layouts.app')

@section('title', 'Edit Case – ' . $case->title)

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('cases.show', $case) }}" class="text-sm text-teal-700 hover:underline">← Back</a>
    <h1 class="text-2xl font-semibold text-stone-800">Edit Case</h1>
</div>

<div class="max-w-xl bg-white rounded-xl shadow-sm border border-stone-200 p-6">
    <form method="POST" action="{{ route('cases.update', $case) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="patient_id" class="block text-sm font-medium text-stone-700 mb-1">Patient <span class="text-red-500">*</span></label>
            <select id="patient_id" name="patient_id" required
                    class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('patient_id') border-red-400 @enderror">
                @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id', $case->patient_id) == $patient->id ? 'selected' : '' }}>
                    {{ $patient->name }}
                    @if($patient->date_of_birth) ({{ $patient->date_of_birth->format('d M Y') }}) @endif
                </option>
                @endforeach
            </select>
            @error('patient_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-stone-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $case->title) }}" required
                   class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('title') border-red-400 @enderror">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-stone-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('description') border-red-400 @enderror">{{ old('description', $case->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-stone-700 mb-1">Internal Notes</label>
            <textarea id="notes" name="notes" rows="2"
                      class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('notes') border-red-400 @enderror">{{ old('notes', $case->notes) }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition shadow">
                Save Changes
            </button>
            <a href="{{ route('cases.show', $case) }}"
               class="px-5 py-2 text-sm text-stone-600 border border-stone-300 rounded-lg hover:bg-stone-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
