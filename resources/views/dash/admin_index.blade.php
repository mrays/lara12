@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

    <p>Welcome, {{ $user->name }} ({{ $user->email }})</p>

    <div class="mt-4">
        <a href="{{ route('client.dashboard') }}" class="underline">Go to Client Dashboard</a>
    </div>
</div>
@endsection
