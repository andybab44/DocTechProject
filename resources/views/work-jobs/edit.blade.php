@extends('layouts.app')

@section('title', __('app.work_job.edit_title') . ' – ' . $workJob->title)

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('work-jobs.show', $workJob) }}" class="text-sm text-indigo-600 hover:underline">{{ __('app.work_job.back') }}</a>
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.work_job.edit_title') }}</h1>
</div>

<div class="max-w-xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('work-jobs.update', $workJob) }}" class="space-y-5">
        @csrf
        @method('PUT')

        @if(auth()->user()->isTechnician())
            {{-- Technicians only update status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_status') }}</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ $workJob->status === $status ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
        @else
            {{-- Doctors and admins --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_title') }} <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $workJob->title) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('title') border-red-400 @enderror">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_description') }}</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('description') border-red-400 @enderror">{{ old('description', $workJob->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_datetime') }} <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                       value="{{ old('scheduled_at', $workJob->scheduled_at->format('Y-m-d\TH:i')) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('scheduled_at') border-red-400 @enderror">
                @error('scheduled_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_technician') }} <span class="text-red-500">*</span></label>
                <select id="technician_id" name="technician_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('technician_id') border-red-400 @enderror">
                    @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ old('technician_id', $workJob->technician_id) == $technician->id ? 'selected' : '' }}>
                        {{ $technician->name }}
                    </option>
                    @endforeach
                </select>
                @error('technician_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_status') }}</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ old('status', $workJob->status->value) === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow">
                {{ __('app.work_job.btn_save') }}
            </button>
            <a href="{{ route('work-jobs.show', $workJob) }}"
               class="px-5 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                {{ __('app.work_job.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
