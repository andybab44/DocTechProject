@extends('layouts.app')

@section('title', __('app.users.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.users.title') }}</h1>
        <p class="text-stone-500 mt-1">{{ __('app.users.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
        class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white
               text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.users.add_user') }}
    </a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

{{-- Search / filter bar --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="{{ __('app.users.search_placeholder') }}"
           class="flex-1 min-w-48 rounded-md border border-stone-300 px-3 py-2 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-teal-400">
    <select name="role"
            class="rounded-md border border-stone-300 px-3 py-2 text-sm shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-teal-400">
        <option value="">{{ __('app.users.filter_all_roles') }}</option>
        @foreach ($roles as $r)
            <option value="{{ $r->value }}" @selected($role === $r->value)>{{ $r->label() }}</option>
        @endforeach
    </select>
    <button type="submit"
            class="rounded-md bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium px-4 py-2 transition">
        {{ __('app.users.btn_filter') }}
    </button>
    @if ($search !== '' || $role !== null)
    <a href="{{ route('admin.users.index') }}"
       class="rounded-md border border-stone-300 text-stone-600 hover:bg-stone-50 text-sm font-medium px-4 py-2 transition">
        {{ __('app.users.btn_reset') }}
    </a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_name') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_email') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_role') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_status') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_jobs_created') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_jobs_assigned') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_avg_rating') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.users.col_created') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse ($users as $user)
            <tr class="hover:bg-stone-50 {{ $user->is_active ? '' : 'opacity-60' }}">
                <td class="px-6 py-4 font-medium text-stone-800">{{ $user->name }}</td>
                <td class="px-6 py-4 text-stone-600">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @php
                        $colours = [
                            'admin'      => 'bg-teal-100 text-teal-800',
                            'doctor'     => 'bg-blue-100 text-blue-700',
                            'technician' => 'bg-orange-100 text-orange-700',
                        ];
                    @endphp
                    <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5
                                 {{ $colours[$user->role->value] ?? 'bg-stone-100 text-stone-700' }}">
                        {{ $user->role->label() }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($user->is_active)
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-green-100 text-green-700">{{ __('app.users.status_active') }}</span>
                    @else
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-red-100 text-red-600">{{ __('app.users.status_inactive') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-stone-600 text-center">{{ $user->work_jobs_as_doctor_count }}</td>
                <td class="px-6 py-4 text-stone-600 text-center">{{ $user->work_jobs_as_technician_count }}</td>
                <td class="px-6 py-4 text-stone-600 text-center">
                    @if($user->average_rating !== null)
                        {{ number_format($user->average_rating, 1) }} / 5
                    @else
                        &mdash;
                    @endif
                </td>
                <td class="px-6 py-4 text-stone-500">{{ $user->created_at->toFormattedDateString() }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-sm text-teal-700 hover:underline">{{ __('app.users.btn_edit') }}</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}"
                              onsubmit="return confirm('{{ $user->is_active ? __('app.users.deactivate_confirm') : __('app.users.activate_confirm') }}')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="text-sm {{ $user->is_active ? 'text-red-500 hover:underline' : 'text-green-600 hover:underline' }}">
                                {{ $user->is_active ? __('app.users.deactivate') : __('app.users.activate') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-6 py-8 text-center text-stone-400">{{ __('app.users.no_users') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-stone-100">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
