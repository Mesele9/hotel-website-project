@extends('layouts.admin')

@section('title', 'Edit Room')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Edit Room: {{ $room->room_number }}</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Room Number --}}
        <div class="mb-4">
            <label for="room_number" class="block text-gray-700 text-sm font-bold mb-2">Room Number:</label>
            <input type="text" name="room_number" id="room_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('room_number', $room->room_number) }}" required>
            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Room Type --}}
        <div class="mb-4">
            <label for="room_type_id" class="block text-gray-700 text-sm font-bold mb-2">Room Type:</label>
            <select name="room_type_id" id="room_type_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('room_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-6">
            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
            <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="Available" {{ old('status', $room->status) == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Occupied" {{ old('status', $room->status) == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="Under Maintenance" {{ old('status', $room->status) == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
            </select>
            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Room
            </button>
        </div>
    </form>
</div>
@endsection