@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Client Dashboard</h1>

    <p>Welcome, {{ $user->name }} ({{ $user->email }})</p>

    <div class="mt-4">
        @if($user->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="underline">Go to Admin Dashboard</a>
        @endif
    </div>
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button
        type="submit"
        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
    >
        Logout
    </button>
</form>
</div>
@endsection
