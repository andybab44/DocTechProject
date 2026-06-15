@extends('layouts.app')

@section('title', $workJob->title)

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('work-jobs.calendar') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.work_job.back_calendar') }}</a>
    <h1 class="text-2xl font-semibold text-stone-800">{{ $workJob->title }}</h1>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="max-w-xl space-y-6">
    {{-- Details card --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 space-y-4">
        @php $color = $workJob->status->color(); @endphp

        <div class="flex items-center justify-between">
            <span class="job-pill job-{{ $color }}" style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:600;">
                {{ $workJob->status->label() }}
            </span>
            <span class="text-sm text-stone-500">{{ $workJob->scheduled_at->format('D d M Y, H:i') }}</span>
        </div>

        @if($workJob->description)
        <p class="text-sm text-stone-700 whitespace-pre-wrap">{{ $workJob->description }}</p>
        @endif

        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <dt class="text-stone-500 font-medium">{{ __('app.work_job.details_requested_by') }}</dt>
            <dd class="text-stone-800">{{ $workJob->doctor->name }}</dd>
            <dt class="text-stone-500 font-medium">{{ __('app.work_job.details_assigned_to') }}</dt>
            <dd class="text-stone-800">{{ $workJob->technician->name }}</dd>
        </dl>
    </div>

    {{-- Status update (technician or doctor or admin) --}}
    @php
        $canUpdateStatus = (auth()->user()->isTechnician() && $workJob->technician_id === auth()->id())
            || (auth()->user()->isDoctor() && $workJob->doctor_id === auth()->id())
            || auth()->user()->isAdmin();
        $hasTransitions = count($statuses) > 1 || (count($statuses) === 1 && $statuses[0] !== $workJob->status);
    @endphp
    @if($canUpdateStatus && count($statuses) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <h2 class="text-sm font-semibold text-stone-700 mb-3">{{ __('app.work_job.update_status') }}</h2>
        <form method="POST" action="{{ route('work-jobs.update', $workJob) }}" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-3">
                <select name="status"
                        class="rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ $workJob->status === $status ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-teal-700 text-white text-sm rounded-lg hover:bg-teal-800 transition">
                    {{ __('app.work_job.btn_save_status') }}
                </button>
            </div>
            <div>
                <input type="text" name="status_notes" placeholder="{{ __('app.work_job.status_notes_placeholder') }}"
                       maxlength="500"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>
        </form>
    </div>
    @endif

    {{-- Edit / Delete (doctor or admin) --}}
    @if(auth()->user()->isAdmin() || (auth()->user()->isDoctor() && $workJob->doctor_id === auth()->id()))
    <div class="flex items-center gap-3">
        <a href="{{ route('work-jobs.edit', $workJob) }}"
           class="px-4 py-2 text-sm bg-white border border-stone-300 rounded-lg shadow-sm hover:bg-stone-50 transition">
            {{ __('app.work_job.btn_edit') }}
        </a>
        <form method="POST" action="{{ route('work-jobs.destroy', $workJob) }}"
              onsubmit="return confirm('{{ __('app.work_job.delete_confirm') }}')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                {{ __('app.work_job.btn_delete') }}
            </button>
        </form>
    </div>
    @endif

    {{-- Status Timeline --}}
    @if($workJob->statusHistory->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <h2 class="text-sm font-semibold text-stone-700 mb-4">Status History</h2>
        <ol class="relative border-l border-stone-200 space-y-4 ml-2">
            @foreach($workJob->statusHistory as $entry)
            @php $toColor = $entry->to_status->color(); @endphp
            <li class="ml-4">
                <span class="absolute -left-1.5 flex items-center justify-center w-3 h-3 rounded-full
                    job-{{ $toColor }}" style="top: 0.25rem;"></span>
                <div class="flex items-center gap-2 flex-wrap">
                    @if($entry->from_status)
                    <span class="job-pill job-{{ $entry->from_status->color() }}"
                          style="display:inline-flex;align-items:center;padding:1px 8px;border-radius:9999px;font-size:11px;font-weight:600;">
                        {{ $entry->from_status->label() }}
                    </span>
                    <span class="text-stone-400 text-xs">→</span>
                    @endif
                    <span class="job-pill job-{{ $toColor }}"
                          style="display:inline-flex;align-items:center;padding:1px 8px;border-radius:9999px;font-size:11px;font-weight:600;">
                        {{ $entry->to_status->label() }}
                    </span>
                    <span class="text-xs text-stone-500">by {{ $entry->changedBy->name }}</span>
                    <span class="text-xs text-stone-400">{{ $entry->created_at->toFormattedDateString() }} {{ $entry->created_at->format('H:i') }}</span>
                </div>
                @if($entry->notes)
                <p class="mt-1 text-xs text-stone-600 italic">{{ $entry->notes }}</p>
                @endif
            </li>
            @endforeach
        </ol>
    </div>
    @endif

    {{-- Attachments --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-stone-700">{{ __('app.work_job.attachments') }}</h2>
            <button type="button" onclick="openUploadModal()"
                    class="px-3 py-1.5 bg-teal-700 text-white text-xs font-medium rounded-lg hover:bg-teal-800 transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('app.work_job.attach_file') }}
            </button>
        </div>

        @if($workJob->attachments->isEmpty())
            <p class="text-sm text-stone-400">{{ __('app.work_job.no_attachments') }}</p>
        @else
        <ul class="divide-y divide-stone-100">
            @foreach($workJob->attachments as $attachment)
            <li class="flex items-center justify-between py-2.5 text-sm">
                <button type="button"
                        onclick="openDownloadModal({{ $attachment->id }}, '{{ e($attachment->original_name) }}', '{{ number_format($attachment->size / 1024, 1) }} KB', '{{ $attachment->created_at->toFormattedDateString() }}', '{{ e($attachment->uploader->name) }}', '{{ route('work-jobs.attachments.download', [$workJob, $attachment]) }}')"
                        class="text-left group">
                    <span class="text-stone-800 font-medium group-hover:text-teal-700 transition">{{ $attachment->original_name }}</span>
                    <span class="ml-2 text-xs text-stone-400">
                        {{ number_format($attachment->size / 1024, 1) }} KB
                        &middot; {{ $attachment->created_at->toFormattedDateString() }}
                        &middot; {{ $attachment->uploader->name }}
                    </span>
                </button>
                @if(auth()->user()->isAdmin() || $attachment->uploaded_by === auth()->id() || (auth()->user()->isDoctor() && $workJob->doctor_id === auth()->id()))
                <button type="button"
                        onclick="openDeleteModal({{ $attachment->id }}, '{{ e($attachment->original_name) }}')"
                        class="ml-3 text-red-400 hover:text-red-600 transition flex-shrink-0" title="Remove">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
                @endif
            </li>
            @endforeach
        </ul>
        @endif

        @error('file')
        <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- ===================== REVIEWS SECTION ===================== --}}
@php
    $hasReviewsModule = ($license = auth()->user()->license) && $license->isValid() && $license->hasModule(\App\Enums\Module::Reviews);
@endphp
@if($hasReviewsModule)
<div class="max-w-xl mt-6 space-y-4">
    {{-- Existing visible reviews for this job --}}
    @if($reviews->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <h2 class="text-sm font-semibold text-stone-700 mb-4">{{ __('app.reviews.job_reviews_title') }}</h2>
        <ul class="divide-y divide-stone-100">
            @foreach($reviews as $review)
            <li class="py-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-stone-800">{{ $review->reviewer->name }}</span>
                    <span class="text-yellow-500 font-semibold text-sm">{{ $review->rating }}/5 ★</span>
                </div>
                @if($review->comment)
                <p class="text-sm text-stone-600">{{ $review->comment }}</p>
                @endif
                <p class="text-xs text-stone-400 mt-1">{{ $review->created_at->toFormattedDateString() }}</p>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Review form (if eligible) --}}
    @if($canReview)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <h2 class="text-sm font-semibold text-stone-700 mb-4">{{ __('app.reviews.leave_review_title') }}</h2>
        <form method="POST" action="{{ route('reviews.store', $workJob) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.reviews.field_rating') }} *</label>
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}"
                               {{ old('rating') == $i ? 'checked' : '' }}
                               class="sr-only peer" required>
                        <span class="text-2xl peer-checked:text-yellow-400 text-stone-300 hover:text-yellow-300 transition select-none">★</span>
                    </label>
                    @endfor
                </div>
                @error('rating')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="review_comment" class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.reviews.field_comment') }}</label>
                <textarea id="review_comment" name="comment" rows="3"
                          class="w-full rounded-md border-stone-300 shadow-sm focus:border-teal-600 focus:ring-teal-500 text-sm @error('comment') border-red-400 @enderror">{{ old('comment') }}</textarea>
                @error('comment')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="inline-flex items-center bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.reviews.btn_submit') }}
            </button>
        </form>
    </div>
    @elseif($existingReview)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4 text-sm text-stone-600 flex items-center gap-2">
        <span class="text-yellow-400 text-xl">★</span>
        {{ __('app.reviews.already_reviewed', ['rating' => $existingReview->rating]) }}
    </div>
    @endif
</div>
@endif

{{-- ===================== UPLOAD MODAL ===================== --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50" onclick="closeUploadModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-stone-800">{{ __('app.work_job.attach_file') }}</h3>
                <button type="button" onclick="closeUploadModal()" class="text-stone-400 hover:text-stone-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <form id="uploadForm" method="POST" action="{{ route('work-jobs.attachments.store', $workJob) }}"
                  enctype="multipart/form-data">
                @csrf
                {{-- Drop zone --}}
                <div id="dropZone"
                     class="relative flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-stone-300 bg-stone-50 p-8 text-center cursor-pointer transition hover:border-teal-400 hover:bg-teal-50"
                     onclick="document.getElementById('fileInput').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-teal-600','bg-teal-50')"
                     ondragleave="this.classList.remove('border-teal-600','bg-teal-50')"
                     ondrop="handleDrop(event)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p id="dropZoneLabel" class="text-sm text-stone-500">
                        {!! __('app.work_job.drop_or_browse', ['link' => '<span class="font-medium text-teal-700">' . __('app.work_job.click_to_browse') . '</span>']) !!}
                    </p>
                    <p class="text-xs text-stone-400">{{ __('app.work_job.drop_hint') }}</p>
                    <input id="fileInput" type="file" name="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.zip" onchange="handleFileSelect(this)">
                </div>

                {{-- Selected file preview --}}
                <div id="filePreview" class="hidden mt-3 flex items-center gap-3 rounded-lg bg-teal-50 border border-teal-100 px-3 py-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p id="previewName" class="text-sm font-medium text-stone-800 truncate"></p>
                        <p id="previewSize" class="text-xs text-stone-500"></p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-stone-400 hover:text-red-500 transition flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" onclick="closeUploadModal()"
                            class="px-4 py-2 text-sm text-stone-600 bg-stone-100 hover:bg-stone-200 rounded-lg transition">
                        {{ __('app.work_job.btn_cancel') }}
                    </button>
                    <button id="uploadSubmitBtn" type="submit" disabled
                            class="px-4 py-2 text-sm text-white bg-teal-700 rounded-lg transition hover:bg-teal-800 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg id="uploadSpinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        {{ __('app.work_job.btn_upload') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== DOWNLOAD MODAL ===================== --}}
<div id="downloadModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50" onclick="closeDownloadModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-stone-800">{{ __('app.work_job.download') }}</h3>
                <button type="button" onclick="closeDownloadModal()" class="text-stone-400 hover:text-stone-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p id="dlFileName" class="text-sm font-semibold text-stone-800 break-all"></p>
                    <p id="dlFileMeta" class="text-xs text-stone-500 mt-0.5"></p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDownloadModal()"
                        class="px-4 py-2 text-sm text-stone-600 bg-stone-100 hover:bg-stone-200 rounded-lg transition">
                    {{ __('app.work_job.btn_cancel') }}
                </button>
                <a id="dlLink" href="#"
                   class="px-4 py-2 text-sm text-white bg-teal-700 hover:bg-teal-800 rounded-lg transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 11.586V3a1 1 0 112 0v8.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    {{ __('app.work_job.download') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ===================== DELETE CONFIRM MODAL ===================== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
            <div class="flex items-start gap-4 mb-5">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-stone-800">{{ __('app.work_job.remove_attachment') }}</h3>
                    <p class="text-sm text-stone-500 mt-1" id="deleteConfirmText"></p>
                </div>
            </div>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 text-sm text-stone-600 bg-stone-100 hover:bg-stone-200 rounded-lg transition">
                        {{ __('app.work_job.btn_cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        {{ __('app.work_job.btn_remove') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ---- Upload Modal ----
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    clearFile();
}
function handleFileSelect(input) {
    if (input.files && input.files[0]) showFilePreview(input.files[0]);
}
function handleDrop(event) {
    event.preventDefault();
    document.getElementById('dropZone').classList.remove('border-teal-600', 'bg-teal-50');
    const file = event.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('fileInput').files = dt.files;
    showFilePreview(file);
}
function showFilePreview(file) {
    document.getElementById('dropZoneLabel').textContent = '{{ __('app.work_job.file_selected') }}';
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
    document.getElementById('filePreview').classList.remove('hidden');
    document.getElementById('uploadSubmitBtn').disabled = false;
}
function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('uploadSubmitBtn').disabled = true;
    document.getElementById('dropZoneLabel').innerHTML = '{!! addslashes(__('app.work_job.drop_or_browse', ['link' => '<span class="font-medium text-teal-700">' . __('app.work_job.click_to_browse') . '</span>'])) !!}';
}
document.getElementById('uploadForm').addEventListener('submit', function() {
    document.getElementById('uploadSpinner').classList.remove('hidden');
    document.getElementById('uploadSubmitBtn').disabled = true;
});

// ---- Download Modal ----
function openDownloadModal(id, name, size, date, uploader, url) {
    document.getElementById('dlFileName').textContent = name;
    document.getElementById('dlFileMeta').textContent = size + ' · ' + date + ' · ' + uploader;
    document.getElementById('dlLink').href = url;
    document.getElementById('downloadModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeDownloadModal() {
    document.getElementById('downloadModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// ---- Delete Modal ----
function openDeleteModal(id, name) {
    document.getElementById('deleteConfirmText').innerHTML = '{{ __('app.work_job.remove_confirm', ['name' => '<span id="deleteFileName" class="font-medium text-stone-700"></span>']) }}';
    document.getElementById('deleteFileName').textContent = name;
    document.getElementById('deleteForm').action = '{{ route('work-jobs.attachments.destroy', [$workJob, '__ID__']) }}'.replace('__ID__', id);
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadModal();
        closeDownloadModal();
        closeDeleteModal();
    }
});

// Auto-open upload modal if there was a file validation error
@error('file')
openUploadModal();
@enderror
</script>
@endpush
