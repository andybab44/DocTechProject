@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Doctor Dashboard</h1>
    <p class="text-gray-500 mt-1">Welcome back, Dr. {{ auth()->user()->name }}.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Role</p>
        <p class="mt-1 text-2xl font-bold text-blue-600">Doctor</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Access Level</p>
        <p class="mt-1 text-2xl font-bold text-green-600">Clinical</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Status</p>
        <p class="mt-1 text-2xl font-bold text-yellow-600">Active</p>
    </div>
</div>
@endsection
