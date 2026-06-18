@extends('layouts.app')

@section('title', __('app.vendors.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.vendors.title') }}</h1>
        <p class="text-stone-500 mt-1">{{ __('app.vendors.subtitle') }}</p>
    </div>
    <a href="{{ route('admin.vendors.create') }}"
       class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white
              text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.vendors.add_vendor') }}
    </a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <table class="min-w-full divide-y divide-stone-200 text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.vendors.col_name') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.vendors.col_email') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.vendors.col_phone') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.vendors.col_items') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse ($vendors as $vendor)
            <tr class="hover:bg-stone-50">
                <td class="px-6 py-4 font-medium text-stone-800">{{ $vendor->name }}</td>
                <td class="px-6 py-4 text-stone-600">{{ $vendor->email }}</td>
                <td class="px-6 py-4 text-stone-500">{{ $vendor->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-600">{{ $vendor->items_count }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.vendors.edit', $vendor) }}"
                           class="text-sm text-teal-700 hover:underline">{{ __('app.vendors.btn_edit') }}</a>
                        <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}"
                              onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">{{ __('app.vendors.btn_delete') }}</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-stone-400">{{ __('app.vendors.no_vendors') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($vendors->hasPages())
    <div class="mt-4">{{ $vendors->links() }}</div>
@endif
@endsection
