@extends('layouts.app')

@section('title', __('app.inventory.edit_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('inventory.show', $inventoryItem) }}" class="text-sm text-teal-700 hover:underline">← {{ $inventoryItem->name }}</a>
    <h1 class="text-2xl font-semibold text-stone-800 mt-1">{{ __('app.inventory.edit_title') }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.inventory.update', $inventoryItem) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_name') }}</label>
            <input type="text" name="name" value="{{ old('name', $inventoryItem->name) }}" required
                   class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                          @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_description') }}</label>
            <textarea name="description" rows="3"
                      class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                             @error('description') border-red-400 @enderror">{{ old('description', $inventoryItem->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_unit') }}</label>
                <input type="text" name="unit" value="{{ old('unit', $inventoryItem->unit) }}" required
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                              @error('unit') border-red-400 @enderror">
                @error('unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_category') }}</label>
                <input type="text" name="category" value="{{ old('category', $inventoryItem->category) }}"
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                              @error('category') border-red-400 @enderror">
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_threshold') }}</label>
            <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $inventoryItem->low_stock_threshold) }}" min="0" required
                   class="w-40 rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                          @error('low_stock_threshold') border-red-400 @enderror">
            @error('low_stock_threshold')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">{{ __('app.inventory.field_vendor') }}</label>
            <select name="vendor_id"
                    class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400
                           @error('vendor_id') border-red-400 @enderror">
                <option value="">{{ __('app.inventory.select_vendor') }}</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(old('vendor_id', $inventoryItem->vendor_id) == $vendor->id)>
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>
            @error('vendor_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-md px-5 py-2 transition">
                {{ __('app.inventory.btn_save') }}
            </button>
            <a href="{{ route('inventory.show', $inventoryItem) }}"
               class="text-sm text-stone-600 border border-stone-300 hover:bg-stone-50 rounded-md px-5 py-2 transition">
                {{ __('app.inventory.btn_cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
