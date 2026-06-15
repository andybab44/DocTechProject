@extends('layouts.app')

@section('title', __('app.licenses.edit_title'))

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.licenses.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.licenses.back') }}</a>
    <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.licenses.edit_title') }}</h1>
</div>

<div class="max-w-lg space-y-5">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        {{-- User info (read-only) --}}
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-stone-100">
            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-semibold text-sm flex-shrink-0">
                {{ mb_strtoupper(mb_substr($license->user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-stone-800">{{ $license->user->name }}</p>
                <p class="text-xs text-stone-500">{{ $license->user->email }} &middot; {{ $license->user->role->label() }}</p>
            </div>
            <div class="ml-auto">
                @if($license->isValid())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ __('app.licenses.status_active') }}</span>
                @elseif(!$license->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">{{ __('app.licenses.status_inactive') }}</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ __('app.licenses.status_expired') }}</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.licenses.update', $license) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Expiry --}}
            <div>
                <label for="expires_at" class="block text-sm font-medium text-stone-700 mb-1">
                    {{ __('app.licenses.field_expires') }}
                    <span class="text-stone-400 font-normal">{{ __('app.licenses.field_expires_hint') }}</span>
                </label>
                <input type="date" id="expires_at" name="expires_at"
                       value="{{ old('expires_at', $license->expires_at?->format('Y-m-d')) }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('expires_at') border-red-400 @enderror">
                @error('expires_at')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modules --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">{{ __('app.licenses.field_modules') }}</label>
                <div class="space-y-2">
                    @foreach($modules as $module)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="modules[]" value="{{ $module->value }}"
                               {{ in_array($module->value, old('modules', $license->modules ?? [])) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-stone-300 text-teal-700 focus:ring-teal-500">
                        <span class="text-sm text-stone-700 group-hover:text-teal-700 transition">{{ $module->label() }}</span>
                    </label>
                    @endforeach
                </div>
                @error('modules.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition shadow">
                    {{ __('app.licenses.btn_save') }}
                </button>
                <a href="{{ route('admin.licenses.index') }}"
                   class="px-5 py-2 text-sm text-stone-600 border border-stone-300 rounded-lg hover:bg-stone-50 transition">
                    {{ __('app.licenses.btn_cancel') }}
                </a>
            </div>
        </form>
    </div>

    {{-- Toggle active --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-stone-800">{{ __('app.licenses.license_status_label') }}</p>
            <p class="text-xs text-stone-500 mt-0.5">
                {{ $license->is_active ? __('app.licenses.license_status_active') : __('app.licenses.license_status_inactive') }}
            </p>
        </div>
        <form method="POST" action="{{ route('admin.licenses.toggle-active', $license) }}">
            @csrf
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition
                           {{ $license->is_active
                               ? 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200'
                               : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200' }}">
                {{ $license->is_active ? __('app.licenses.deactivate') : __('app.licenses.activate') }}
            </button>
        </form>
    </div>
</div>
@endsection
