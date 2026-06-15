@extends('layouts.app')

@section('title', $inventoryItem->name)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('inventory.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.inventory.back_to_list') }}</a>
        <h1 class="text-2xl font-semibold text-stone-800 mt-1">{{ $inventoryItem->name }}</h1>
        @if($inventoryItem->category)
            <p class="text-stone-500 mt-0.5 text-sm">{{ $inventoryItem->category }}</p>
        @endif
    </div>
    @if(auth()->user()->isAdmin())
    <div class="flex gap-3">
        <a href="{{ route('admin.inventory.edit', $inventoryItem) }}"
           class="text-sm text-teal-700 border border-indigo-300 hover:bg-teal-50 rounded-md px-4 py-2 transition">
            {{ __('app.inventory.btn_edit') }}
        </a>
    </div>
    @endif
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

{{-- Stock card --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs font-medium text-stone-400 uppercase tracking-wide">{{ __('app.inventory.current_stock') }}</p>
        <p class="mt-1 text-3xl font-bold {{ $inventoryItem->isLowStock() ? 'text-red-600' : 'text-stone-800' }}">
            {{ $inventoryItem->quantity }}
            <span class="text-lg font-normal text-stone-500">{{ $inventoryItem->unit }}</span>
        </p>
        @if($inventoryItem->quantity === 0)
            <span class="mt-2 inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-red-100 text-red-700">{{ __('app.inventory.status_out_of_stock') }}</span>
        @elseif($inventoryItem->isLowStock())
            <span class="mt-2 inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-yellow-100 text-yellow-700">{{ __('app.inventory.status_low_stock') }}</span>
        @else
            <span class="mt-2 inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-green-100 text-green-700">{{ __('app.inventory.status_in_stock') }}</span>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs font-medium text-stone-400 uppercase tracking-wide">{{ __('app.inventory.low_stock_threshold') }}</p>
        <p class="mt-1 text-3xl font-bold text-stone-800">
            {{ $inventoryItem->low_stock_threshold }}
            <span class="text-lg font-normal text-stone-500">{{ $inventoryItem->unit }}</span>
        </p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs font-medium text-stone-400 uppercase tracking-wide">{{ __('app.inventory.total_usages') }}</p>
        <p class="mt-1 text-3xl font-bold text-stone-800">{{ $usages->total() }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Description --}}
    @if($inventoryItem->description)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 lg:col-span-1">
        <h2 class="text-sm font-semibold text-stone-700 mb-2">{{ __('app.inventory.field_description') }}</h2>
        <p class="text-sm text-stone-600 whitespace-pre-wrap">{{ $inventoryItem->description }}</p>
    </div>
    @endif

    {{-- Restock form (admins only) --}}
    @if(auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 {{ $inventoryItem->description ? '' : 'lg:col-span-1' }}">
        <h2 class="text-sm font-semibold text-stone-700 mb-3">{{ __('app.inventory.restock_title') }}</h2>
        <form method="POST" action="{{ route('admin.inventory.restock', $inventoryItem) }}" class="flex gap-3">
            @csrf
            <input type="number" name="quantity" min="1" value="1"
                   class="w-28 rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md px-4 py-2 transition">
                {{ __('app.inventory.btn_restock') }}
            </button>
        </form>
    </div>
    @endif

    {{-- Log usage form --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 {{ ($inventoryItem->description || auth()->user()->isAdmin()) ? '' : 'lg:col-span-2' }}">
        <h2 class="text-sm font-semibold text-stone-700 mb-3">{{ __('app.inventory.log_usage_title') }}</h2>
        <form method="POST" action="{{ route('inventory.usage.store', $inventoryItem) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">{{ __('app.inventory.field_quantity_used') }}</label>
                <input type="number" name="quantity_used" min="1" max="{{ $inventoryItem->quantity }}"
                       value="{{ old('quantity_used', 1) }}"
                       class="w-28 rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">{{ __('app.inventory.field_work_job') }}</label>
                <select name="work_job_id"
                        class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <option value="">{{ __('app.inventory.select_work_job') }}</option>
                    @foreach($workJobs as $job)
                        <option value="{{ $job->id }}" @selected(old('work_job_id') == $job->id)>
                            #{{ $job->id }} — {{ $job->title }} ({{ $job->scheduled_at->toFormattedDateString() }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">{{ __('app.inventory.field_notes') }}</label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">{{ old('notes') }}</textarea>
            </div>
            <button type="submit"
                    class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-md px-4 py-2 transition"
                    @if($inventoryItem->quantity === 0) disabled @endif>
                {{ __('app.inventory.btn_log_usage') }}
            </button>
        </form>
    </div>
</div>

{{-- Usage history --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-stone-100">
        <h2 class="text-sm font-semibold text-stone-700">{{ __('app.inventory.usage_history') }}</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_date') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_used_by') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_qty_used') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_work_job') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_notes') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse ($usages as $usage)
            <tr class="hover:bg-stone-50">
                <td class="px-6 py-4 text-stone-500">{{ $usage->created_at->toFormattedDateString() }}</td>
                <td class="px-6 py-4 text-stone-800">{{ $usage->user->name }}</td>
                <td class="px-6 py-4 font-medium text-stone-800">{{ $usage->quantity_used }} {{ $inventoryItem->unit }}</td>
                <td class="px-6 py-4 text-stone-500">
                    @if($usage->workJob)
                        #{{ $usage->workJob->id }} — {{ $usage->workJob->title }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-6 py-4 text-stone-500">{{ $usage->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-stone-400">{{ __('app.inventory.no_usage') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($usages->hasPages())
    <div class="px-6 py-4 border-t border-stone-100">
        {{ $usages->links() }}
    </div>
    @endif
</div>
@endsection
