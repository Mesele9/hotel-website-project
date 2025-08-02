@extends('layouts.admin')

@section('title', 'Create Room')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Create New Room</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('admin.rooms.store') }}" method="POST">
        @csrf
        
        {{-- Room Number --}}
        <div class="mb-4">
            <label for="room_number" class="block text-gray-700 text-sm font-bold mb-2">Room Number:</label>
            <input type="text" name="room_number" id="room_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('room_number') }}" required>
            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Room Type --}}
        <div class="mb-6">
            <label for="room_type_id" class="block text-gray-700 text-sm font-bold mb-2">Room Type:</label>
            <select name="room_type_id" id="room_type_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">-- Select a Room Type --</option>
                @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('room_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Status is defaulted in the migration, so no need for an input here. --}}

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save Room
            </button>
        </div>
    </form>
</div>
@endsection