@extends('layouts.app')

@section('title', __('app.inventory.create_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('app.inventory.back_to_list') }}</a>
    <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ __('app.inventory.create_title') }}</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                          @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_description') }}</label>
            <textarea name="description" rows="3"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                             @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_quantity') }}</label>
                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" min="0" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                              @error('quantity') border-red-400 @enderror">
                @error('quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_unit') }}</label>
                <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                              @error('unit') border-red-400 @enderror"
                       placeholder="pcs, ml, g, box…">
                @error('unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_category') }}</label>
                <input type="text" name="category" value="{{ old('category') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                              @error('category') border-red-400 @enderror">
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.inventory.field_threshold') }}</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" min="0" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400
                              @error('low_stock_threshold') border-red-400 @enderror">
                @error('low_stock_threshold')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.inventory.btn_create') }}
            </button>
            <a href="{{ route('inventory.index') }}"
               class="text-sm text-gray-600 border border-gray-300 hover:bg-gray-50 rounded-md px-5 py-2 transition">
                {{ __('app.inventory.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
