@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-stone-800">Notifications</h1>
    @if(auth()->user()->unreadNotifications->isNotEmpty())
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
        @csrf
        <button type="submit"
                class="px-4 py-2 text-sm bg-teal-700 text-white rounded-lg hover:bg-teal-800 transition">
            Mark all as read
        </button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

@if($notifications->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8 text-center text-stone-400 text-sm">
    No notifications yet.
</div>
@else
<div class="bg-white rounded-xl shadow divide-y divide-stone-100">
    @foreach($notifications as $notification)
    @php
        $data    = $notification->data;
        $isUnread = is_null($notification->read_at);
    @endphp
    <div class="flex items-start gap-4 px-5 py-4 {{ $isUnread ? 'bg-teal-50' : '' }}">
        <div class="flex-shrink-0 mt-0.5">
            @if($isUnread)
            <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
            @else
            <span class="inline-block w-2 h-2 rounded-full bg-gray-200"></span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-stone-800">
                <span class="font-medium">{{ $data['changed_by_name'] ?? 'Someone' }}</span>
                updated
                <span class="font-medium">{{ $data['work_job_title'] ?? 'a work job' }}</span>
                to
                <span class="font-semibold">{{ $data['new_status_label'] ?? $data['new_status'] ?? '' }}</span>.
            </p>
            <p class="text-xs text-stone-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        <a href="{{ route('notifications.read', $notification->id) }}"
           class="flex-shrink-0 text-xs text-teal-700 hover:underline whitespace-nowrap">
            {{ $isUnread ? 'View & mark read' : 'View' }}
        </a>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endif
@endsection
