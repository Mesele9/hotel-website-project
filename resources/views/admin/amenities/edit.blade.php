@extends('layouts.admin')

@section('title', 'Edit Amenity')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Edit Amenity: {{ $amenity->name }}</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('admin.amenities.update', $amenity->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('name', $amenity->name) }}" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Icon --}}
        <div class="mb-6">
            <label for="icon" class="block text-gray-700 text-sm font-bold mb-2">Icon (Optional):</label>
            <input type="text" name="icon" id="icon" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('icon', $amenity->icon) }}" placeholder="e.g., 'fa-solid fa-wifi'">
            <p class="text-gray-600 text-xs italic mt-2">Enter a CSS class name for the icon (e.g., from Font Awesome).</p>
            @error('icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Amenity
            </button>
        </div>
    </form>
</div>
@endsection