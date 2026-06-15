@extends('layouts.app')

@section('title', __('app.licenses.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.licenses.title') }}</h1>
        <p class="text-sm text-stone-500 mt-1">{{ __('app.licenses.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.licenses.create') }}"
       class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-lg px-4 py-2 transition">
        {{ __('app.licenses.new_license') }}
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_user') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_role') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_status') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_modules') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_expires') }}</th>
                <th class="px-6 py-3 text-right font-medium text-stone-500 uppercase tracking-wider">{{ __('app.licenses.col_actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($users as $user)
            <tr class="hover:bg-stone-50 transition">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-stone-500">{{ $user->role->label() }}</td>

                <td class="px-6 py-4">
                    @if(!$user->license)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-stone-100 text-stone-500">{{ __('app.licenses.status_none') }}</span>
                    @elseif($user->license->isValid())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ __('app.licenses.status_active') }}</span>
                    @elseif(!$user->license->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">{{ __('app.licenses.status_inactive') }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ __('app.licenses.status_expired') }}</span>
                    @endif
                </td>

                <td class="px-6 py-4">
                    @if($user->license && count($user->license->modules ?? []))
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->license->modules as $mod)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-teal-50 text-teal-800">
                                {{ \App\Enums\Module::from($mod)->label() }}
                            </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-stone-400 text-xs">{{ __('app.licenses.no_modules') }}</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-stone-500 text-xs">
                    @if($user->license)
                        {{ $user->license->expires_at ? $user->license->expires_at->toFormattedDateString() : __('app.licenses.perpetual') }}
                    @else
                        {{ __('app.licenses.no_modules') }}
                    @endif
                </td>

                <td class="px-6 py-4 text-right">
                    @if(!$user->license)
                        <a href="{{ route('admin.licenses.create', ['user_id' => $user->id]) }}"
                           class="text-teal-700 hover:underline text-xs font-medium">{{ __('app.licenses.assign') }}</a>
                    @else
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.licenses.edit', $user->license) }}"
                               class="text-teal-700 hover:underline text-xs font-medium">{{ __('app.licenses.edit') }}</a>
                            <form method="POST" action="{{ route('admin.licenses.toggle-active', $user->license) }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium {{ $user->license->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }} transition">
                                    {{ $user->license->is_active ? __('app.licenses.deactivate') : __('app.licenses.activate') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div class="mt-4">{{ $users->links() }}</div>
@endif
@endsection
