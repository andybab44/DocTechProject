@extends('layouts.app')

@section('title', __('app.patients.title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.patients.title') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('app.patients.subtitle') }}</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('patients.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
              text-sm font-medium rounded-md px-4 py-2 transition">
        {{ __('app.patients.add_patient') }}
    </a>
    @endif
</div>

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.patients.col_name') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.patients.col_dob') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.patients.col_email') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.patients.col_phone') }}</th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('app.patients.col_appointments') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($patients as $patient)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $patient->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $patient->email ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $patient->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $patient->appointments_count }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('patients.show', $patient) }}"
                           class="text-sm text-indigo-600 hover:underline">{{ __('app.patients.btn_view') }}</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('patients.edit', $patient) }}"
                           class="text-sm text-gray-600 hover:underline">{{ __('app.patients.btn_edit') }}</a>
                        <form method="POST" action="{{ route('patients.destroy', $patient) }}"
                              onsubmit="return confirm('{{ __('app.patients.delete_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">{{ __('app.patients.btn_delete') }}</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">{{ __('app.patients.no_patients') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($patients->hasPages())
<div class="mt-4">{{ $patients->links() }}</div>
@endif
@endsection
