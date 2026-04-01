@extends('layouts.app')

@section('title', $workJob->title)

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('work-jobs.calendar') }}" class="text-sm text-indigo-600 hover:underline">← Back to Calendar</a>
    <h1 class="text-2xl font-semibold text-gray-800">{{ $workJob->title }}</h1>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="max-w-xl space-y-6">
    {{-- Details card --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        @php $color = $workJob->status->color(); @endphp

        <div class="flex items-center justify-between">
            <span class="job-pill job-{{ $color }}" style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:600;">
                {{ $workJob->status->label() }}
            </span>
            <span class="text-sm text-gray-500">{{ $workJob->scheduled_at->format('D d M Y, H:i') }}</span>
        </div>

        @if($workJob->description)
        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $workJob->description }}</p>
        @endif

        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <dt class="text-gray-500 font-medium">Requested by</dt>
            <dd class="text-gray-800">{{ $workJob->doctor->name }}</dd>
            <dt class="text-gray-500 font-medium">Assigned to</dt>
            <dd class="text-gray-800">{{ $workJob->technician->name }}</dd>
        </dl>
    </div>

    {{-- Status update (technician) --}}
    @if(auth()->user()->isTechnician() && $workJob->technician_id === auth()->id())
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Update Status</h2>
        <form method="POST" action="{{ route('work-jobs.update', $workJob) }}" class="flex items-center gap-3">
            @csrf
            @method('PUT')
            <select name="status"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ $workJob->status === $status ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                Save
            </button>
        </form>
    </div>
    @endif

    {{-- Edit / Delete (doctor or admin) --}}
    @if(auth()->user()->isAdmin() || (auth()->user()->isDoctor() && $workJob->doctor_id === auth()->id()))
    <div class="flex items-center gap-3">
        <a href="{{ route('work-jobs.edit', $workJob) }}"
           class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
            Edit
        </a>
        <form method="POST" action="{{ route('work-jobs.destroy', $workJob) }}"
              onsubmit="return confirm('Delete this work job?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Delete
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
