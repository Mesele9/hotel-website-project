@extends('layouts.admin')

@section('title', 'Room Types')

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">Manage Room Types</h1>
    <a href="{{ route('admin.room-types.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + Add New Room Type
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Capacity</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Price</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($roomTypes as $roomType)
            <tr>
                <td class="w-1/3 text-left py-3 px-4">{{ $roomType->name }}</td>
                <td class="w-1/3 text-left py-3 px-4">{{ $roomType->capacity }} Guests</td>
                <td class="text-left py-3 px-4">${{ number_format($roomType->base_price, 2) }}</td>
                <td class="text-left py-3 px-4">
                    <a href="{{ route('admin.room-types.edit', $roomType->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                    <form action="{{ route('admin.room-types.destroy', $roomType->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this room type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4">No room types found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection