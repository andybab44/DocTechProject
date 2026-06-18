@extends('layouts.app')

@section('title', __('app.reorder.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('inventory.index') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.inventory.back_to_list') }}</a>
        <h1 class="text-2xl font-semibold text-stone-800 mt-1">{{ __('app.reorder.title') }}</h1>
        <p class="text-stone-500 mt-1">{{ __('app.reorder.subtitle') }}</p>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

@if (session('warning'))
    <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-700">
        {{ session('warning') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('inventory.reorder.send') }}">
    @csrf

    {{-- Items with a vendor, grouped by vendor --}}
    @forelse ($withVendor as $vendorId => $vendorItems)
    @php $vendor = $vendorItems->first()->vendor; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 mb-6 overflow-hidden">
        <div class="px-6 py-3 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
            <div>
                <span class="font-semibold text-stone-800">{{ $vendor->name }}</span>
                <span class="ml-2 text-sm text-stone-500">{{ $vendor->email }}</span>
            </div>
        </div>
        <table class="min-w-full divide-y divide-stone-100 text-sm">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-4 py-3 w-8"></th>
                    <th class="px-4 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_name') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_quantity') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.inventory.col_threshold') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reorder.col_request_qty') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendorItems as $i => $item)
                @php
                    $key      = "items[{$item->id}]";
                    $checked  = $item->isLowStock();
                @endphp
                <tr class="hover:bg-stone-50" id="row-{{ $item->id }}">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox"
                               name="item_check[{{ $item->id }}]"
                               id="check-{{ $item->id }}"
                               value="1"
                               @checked($checked)
                               class="rounded border-stone-300 text-teal-600 focus:ring-teal-400"
                               onchange="toggleRow({{ $item->id }})">
                        <input type="hidden"
                               id="hidden-id-{{ $item->id }}"
                               name="{{ $key }}[inventory_item_id]"
                               value="{{ $item->id }}"
                               @if(!$checked) disabled @endif>
                    </td>
                    <td class="px-4 py-3 font-medium text-stone-800">{{ $item->name }}</td>
                    <td class="px-4 py-3 {{ $item->isLowStock() ? 'text-red-600 font-semibold' : 'text-stone-600' }}">
                        {{ $item->quantity }} {{ $item->unit }}
                        @if ($item->isLowStock())
                            <span class="ml-1 text-xs bg-yellow-100 text-yellow-700 rounded-full px-2 py-0.5">{{ __('app.inventory.status_low_stock') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-stone-500">{{ $item->low_stock_threshold }} {{ $item->unit }}</td>
                    <td class="px-4 py-3">
                        <input type="number"
                               name="{{ $key }}[quantity]"
                               id="qty-{{ $item->id }}"
                               value="{{ old("{$key}.quantity", max(1, $item->low_stock_threshold - $item->quantity + 1)) }}"
                               min="1"
                               class="w-24 rounded-md border border-stone-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
                               @if(!$checked) disabled @endif>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div class="mb-6 rounded-xl border border-stone-200 bg-white p-8 text-center text-stone-400 text-sm">
        {{ __('app.reorder.no_vendor_assigned') }}
    </div>
    @endforelse

    {{-- Items without a vendor --}}
    @if ($withoutVendor->isNotEmpty())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-6">
        <p class="text-sm font-semibold text-yellow-800 mb-2">{{ __('app.reorder.no_vendor_warning') }}</p>
        <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
            @foreach ($withoutVendor as $item)
                <li>{{ $item->name }} ({{ $item->quantity }} {{ $item->unit }})</li>
            @endforeach
        </ul>
        <p class="mt-2 text-xs text-yellow-600">{{ __('app.reorder.no_vendor_hint') }}</p>
    </div>
    @endif

    @if ($withVendor->isNotEmpty())
    <div class="flex gap-3">
        <button type="submit"
                class="bg-teal-700 hover:bg-teal-800 text-white text-sm font-medium rounded-lg px-5 py-2.5 transition">
            {{ __('app.reorder.btn_send') }}
        </button>
        <a href="{{ route('inventory.index') }}"
           class="text-sm text-stone-600 border border-stone-300 hover:bg-stone-50 rounded-lg px-5 py-2.5 transition">
            {{ __('app.reorder.btn_cancel') }}
        </a>
    </div>
    @endif
</form>

@push('scripts')
<script>
function toggleRow(id) {
    const checked  = document.getElementById('check-' + id).checked;
    const qty      = document.getElementById('qty-' + id);
    const hiddenId = document.getElementById('hidden-id-' + id);
    qty.disabled      = !checked;
    hiddenId.disabled = !checked;
    if (checked) qty.focus();
}
</script>
@endpush
@endsection
