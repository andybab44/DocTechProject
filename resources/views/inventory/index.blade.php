@extends('layouts.app')

@section('title', __('app.inventory.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.inventory.title') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('app.inventory.subtitle') }}</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.inventory.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
              text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.inventory.add_item') }}
    </a>
    @endif
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
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_name') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_category') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_quantity') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_unit') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_threshold') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.inventory.col_status') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($items as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $item->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $item->category ?? '—' }}</td>
                <td class="px-6 py-4 font-semibold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $item->quantity }}
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $item->unit }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $item->low_stock_threshold }}</td>
                <td class="px-6 py-4">
                    @if($item->quantity === 0)
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-red-100 text-red-700">{{ __('app.inventory.status_out_of_stock') }}</span>
                    @elseif($item->isLowStock())
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-yellow-100 text-yellow-700">{{ __('app.inventory.status_low_stock') }}</span>
                    @else
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-green-100 text-green-700">{{ __('app.inventory.status_in_stock') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('inventory.show', $item) }}"
                           class="text-sm text-indigo-600 hover:underline">{{ __('app.inventory.btn_view') }}</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.inventory.edit', $item) }}"
                           class="text-sm text-gray-600 hover:underline">{{ __('app.inventory.btn_edit') }}</a>
                        <form method="POST" action="{{ route('admin.inventory.destroy', $item) }}"
                              onsubmit="return confirm('{{ __('app.inventory.delete_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">{{ __('app.inventory.btn_delete') }}</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">{{ __('app.inventory.no_items') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($items->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
