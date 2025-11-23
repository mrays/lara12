@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl mb-4 font-semibold">Edit Profile</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input name="name" class="border p-2 w-full" value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input name="email" class="border p-2 w-full" value="{{ old('email', $user->email) }}">
            @error('email')
                <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</div>
@endsection
