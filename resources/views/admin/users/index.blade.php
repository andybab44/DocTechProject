@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Users</h1>
        <p class="text-gray-500 mt-1">Manage all users in the system.</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
               text-sm font-medium rounded-md px-4 py-2 transition">
        + Add User
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
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Created</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50">
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
                <td class="px-6 py-4 text-gray-500">{{ $user->created_at->toFormattedDateString() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No users found.</td>
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
