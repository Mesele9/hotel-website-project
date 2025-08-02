@extends('layouts.admin')

@section('title', 'Rooms')

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">Manage Rooms</h1>
    <a href="{{ route('admin.rooms.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        + Add New Room
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
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Room No.</th>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Room Type</th>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($rooms as $room)
            <tr>
                <td class="w-1/4 text-left py-3 px-4">{{ $room->room_number }}</td>
                <td class="w-1/4 text-left py-3 px-4">{{ $room->roomType->name }}</td>
                <td class="w-1/4 text-left py-3 px-4">
                    <span class="px-2 py-1 font-semibold leading-tight rounded-sm
                        @if($room->status == 'Available') bg-green-100 text-green-700
                        @elseif($room->status == 'Occupied') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $room->status }}
                    </span>
                </td>
                <td class="text-left py-3 px-4">
                    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                    <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this room? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4">No rooms found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection