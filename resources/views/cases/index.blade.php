@extends('layouts.app')

@section('title', 'Cases')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-stone-800">Cases</h1>
    <a href="{{ route('cases.create') }}"
       class="inline-flex items-center px-4 py-2 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition shadow">
        New Case
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif

@if($cases->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-8 text-center text-stone-400 text-sm">
    No cases yet. <a href="{{ route('cases.create') }}" class="text-teal-700 hover:underline">Create the first one.</a>
</div>
@else
<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Title</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Patient</th>
                @if(auth()->user()->isAdmin())
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Doctor</th>
                @endif
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Jobs</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Appointments</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($cases as $case)
            <tr class="hover:bg-stone-50 transition">
                <td class="px-5 py-3 font-medium text-gray-900">
                    <a href="{{ route('cases.show', $case) }}" class="hover:text-teal-700">{{ $case->title }}</a>
                </td>
                <td class="px-5 py-3 text-stone-600">{{ $case->patient->name }}</td>
                @if(auth()->user()->isAdmin())
                <td class="px-5 py-3 text-stone-600">{{ $case->doctor->name }}</td>
                @endif
                <td class="px-5 py-3 text-stone-500">{{ $case->work_jobs_count }}</td>
                <td class="px-5 py-3 text-stone-500">{{ $case->appointments_count }}</td>
                <td class="px-5 py-3 text-stone-400">{{ $case->created_at->toFormattedDateString() }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('cases.show', $case) }}" class="text-xs text-teal-700 hover:underline">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $cases->links() }}
</div>
@endif
@endsection
