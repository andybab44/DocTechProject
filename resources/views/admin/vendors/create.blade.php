@extends('layouts.app')

@section('title', __('app.vendors.create_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vendors.index') }}" class="text-sm text-teal-700 hover:underline">← {{ __('app.vendors.title') }}</a>
    <h1 class="text-2xl font-semibold text-stone-800 mt-1">{{ __('app.vendors.create_title') }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.vendors.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.vendors.field_name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                          @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.vendors.field_email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                          @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.vendors.field_phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                          @error('phone') border-red-400 @enderror">
            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.vendors.field_notes') }}</label>
            <textarea name="notes" rows="3"
                      class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                             @error('notes') border-red-400 @enderror">{{ old('notes') }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.vendors.btn_create') }}
            </button>
            <a href="{{ route('admin.vendors.index') }}"
               class="text-sm text-stone-600 border border-stone-300 hover:bg-stone-50 rounded-md px-5 py-2 transition">
                {{ __('app.vendors.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
