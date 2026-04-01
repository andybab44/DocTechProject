@extends('layouts.app')

@section('title', 'Technician Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Technician Dashboard</h1>
    <p class="text-gray-500 mt-1">Welcome back, {{ auth()->user()->name }}.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Role</p>
        <p class="mt-1 text-2xl font-bold text-orange-600">Technician</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Access Level</p>
        <p class="mt-1 text-2xl font-bold text-green-600">Technical</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Status</p>
        <p class="mt-1 text-2xl font-bold text-yellow-600">Active</p>
    </div>
</div>
@endsection
