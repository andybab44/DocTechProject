@extends('layouts.app')

@section('title', __('app.users.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.users.title') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('app.users.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
               text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.users.add_user') }}
    </a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.users.col_name') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.users.col_email') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.users.col_role') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.users.col_status') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.users.col_created') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50 {{ $user->is_active ? '' : 'opacity-60' }}">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    @php
                        $colours = [
                            'admin'      => 'bg-indigo-100 text-indigo-700',
                            'doctor'     => 'bg-blue-100 text-blue-700',
                            'technician' => 'bg-orange-100 text-orange-700',
                        ];
                    @endphp
                    <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5
                                 {{ $colours[$user->role->value] ?? 'bg-gray-100 text-gray-700' }}">
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
                <td class="px-6 py-4 text-gray-500">{{ $user->created_at->toFormattedDateString() }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-sm text-indigo-600 hover:underline">{{ __('app.users.btn_edit') }}</a>
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
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">{{ __('app.users.no_users') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
