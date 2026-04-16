@extends('layouts.app')

@section('title', __('app.work_job.new_title'))

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('work-jobs.calendar') }}" class="text-sm text-indigo-600 hover:underline">{{ __('app.work_job.back_calendar') }}</a>
    <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.work_job.new_title') }}</h1>
</div>

<div class="max-w-xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('work-jobs.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_title') }} <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('title') border-red-400 @enderror">
            @error('title')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_description') }}</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Scheduled at --}}
        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_datetime') }} <span class="text-red-500">*</span></label>
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
            <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.field_technician') }} <span class="text-red-500">*</span></label>
            <select id="technician_id" name="technician_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('technician_id') border-red-400 @enderror">
                <option value="">{{ __('app.work_job.select_technician') }}</option>
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

        {{-- File Attachments --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.work_job.attachments_optional') }} <span class="text-gray-400 font-normal">{{ __('app.work_job.attachments_optional_note') }}</span></label>
            <div id="createDropZone"
                 class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center cursor-pointer transition hover:border-indigo-400 hover:bg-indigo-50"
                 onclick="document.getElementById('createFileInput').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-indigo-500','bg-indigo-50')"
                 ondragleave="this.classList.remove('border-indigo-500','bg-indigo-50')"
                 ondrop="handleCreateDrop(event)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p id="createDropLabel" class="text-sm text-gray-500">
                    {!! __('app.work_job.drop_or_browse', ['link' => '<span class="font-medium text-indigo-600">' . __('app.work_job.click_to_browse') . '</span>']) !!}
                </p>
                <p class="text-xs text-gray-400">{{ __('app.work_job.drop_hint_create') }}</p>
                <input id="createFileInput" type="file" name="files[]" multiple class="sr-only"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.zip"
                       onchange="handleCreateFileSelect(this)">
            </div>
            @error('files.*')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <ul id="createFileList" class="mt-2 divide-y divide-gray-100"></ul>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow">
                {{ __('app.work_job.btn_create') }}
            </button>
            <a href="{{ route('work-jobs.calendar') }}"
               class="px-5 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                {{ __('app.work_job.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function handleCreateDrop(event) {
    event.preventDefault();
    document.getElementById('createDropZone').classList.remove('border-indigo-500', 'bg-indigo-50');
    const files = event.dataTransfer.files;
    if (!files.length) return;
    const dt = new DataTransfer();
    const existing = document.getElementById('createFileInput').files;
    for (let i = 0; i < existing.length; i++) dt.items.add(existing[i]);
    for (let i = 0; i < files.length; i++) dt.items.add(files[i]);
    document.getElementById('createFileInput').files = dt.files;
    updateCreateFileList();
}
function handleCreateFileSelect(input) {
    updateCreateFileList();
}
function updateCreateFileList() {
    const input = document.getElementById('createFileInput');
    const list = document.getElementById('createFileList');
    const label = document.getElementById('createDropLabel');
    list.innerHTML = '';
    if (!input.files.length) {
        label.innerHTML = '{!! addslashes(__('app.work_job.drop_or_browse', ['link' => '<span class=\"font-medium text-indigo-600\">' . __('app.work_job.click_to_browse') . '</span>'])) !!}';
        return;
    }
    label.textContent = input.files.length + ' {{ __('app.work_job.file_selected') }}';
    Array.from(input.files).forEach(function(file, idx) {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between py-1.5 text-xs';
        li.innerHTML = '<span class="text-gray-700 font-medium truncate max-w-xs">' + file.name + '</span>'
            + '<span class="ml-2 text-gray-400 flex-shrink-0">' + (file.size / 1024).toFixed(1) + ' KB</span>';
        list.appendChild(li);
    });
}
</script>
@endpush
