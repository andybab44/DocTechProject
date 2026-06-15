@extends('layouts.app')

@section('title', __('app.licenses.create_title'))

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.licenses.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.licenses.back') }}</a>
    <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.licenses.create_title') }}</h1>
</div>

<div class="max-w-lg bg-white rounded-xl shadow-sm border border-stone-200 p-6">
    <form method="POST" action="{{ route('admin.licenses.store') }}" class="space-y-5">
        @csrf

        {{-- User --}}
        <div>
            <label for="user_id" class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.licenses.field_user') }} <span class="text-red-500">*</span></label>
            <select id="user_id" name="user_id" required
                    class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('user_id') border-red-400 @enderror">
                <option value="">{{ __('app.licenses.select_user') }}</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ (old('user_id', request('user_id')) == $user->id) ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->role->label() }})
                </option>
                @endforeach
            </select>
            @error('user_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @if($users->isEmpty())
            <p class="mt-1 text-xs text-stone-400">{{ __('app.licenses.no_users_available') }}</p>
            @endif
        </div>

        {{-- Expiry --}}
        <div>
            <label for="expires_at" class="block text-sm font-medium text-stone-700 mb-1">
                {{ __('app.licenses.field_expires') }}
                <span class="text-stone-400 font-normal">{{ __('app.licenses.field_expires_hint') }}</span>
            </label>
            <input type="date" id="expires_at" name="expires_at"
                   value="{{ old('expires_at') }}"
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
                           {{ in_array($module->value, old('modules', [])) ? 'checked' : '' }}
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
                {{ __('app.licenses.btn_create') }}
            </button>
            <a href="{{ route('admin.licenses.index') }}"
               class="px-5 py-2 text-sm text-stone-600 border border-stone-300 rounded-lg hover:bg-stone-50 transition">
                {{ __('app.licenses.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
