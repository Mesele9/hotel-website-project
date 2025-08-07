@extends('layouts.admin')
@section('title', 'Event Spaces')

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">Manage Event Spaces</h1>
    <a href="{{ route('admin.event-spaces.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + Add New Space
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Image</th>
                <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Capacity</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($eventSpaces as $space)
            <tr>
                <td class="text-left py-3 px-4">
                    <img src="{{ $space->image ? asset('storage/' . $space->image) : 'https://via.placeholder.com/100x75.png?text=No+Image' }}" alt="{{ $space->name }}" class="h-16 w-24 object-cover rounded">
                </td>
                <td class="w-1/2 text-left py-3 px-4">{{ $space->name }}</td>
                <td class="text-left py-3 px-4">{{ $space->capacity }} Guests</td>
                <td class="text-left py-3 px-4">
                    <a href="{{ route('admin.event-spaces.edit', $space->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                    <form action="{{ route('admin.event-spaces.destroy', $space->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this space?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4">No event spaces found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection