@extends('layouts.admin')

@section('title', 'Edit Room Type')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Edit Room Type: {{ $roomType->name }}</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('admin.room-types.update', $roomType->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('name', $roomType->name) }}" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Description --}}
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
            <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('description', $roomType->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Capacity --}}
            <div class="mb-4">
                <label for="capacity" class="block text-gray-700 text-sm font-bold mb-2">Capacity (Guests):</label>
                <input type="number" name="capacity" id="capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('capacity', $roomType->capacity) }}" required min="1">
                @error('capacity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            {{-- Base Price --}}
            <div class="mb-4">
                <label for="base_price" class="block text-gray-700 text-sm font-bold mb-2">Base Price ($):</label>
                <input type="number" name="base_price" id="base_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('base_price', $roomType->base_price) }}" required min="0" step="0.01">
                @error('base_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Amenities --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Amenities:</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($amenities as $amenity)
                <div class="flex items-center">
                    <input type="checkbox" name="amenities[]" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" class="mr-2"
                        {{ in_array($amenity->id, $roomType->amenities->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <label for="amenity_{{ $amenity->id }}">{{ $amenity->name }}</label>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Existing Images --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Existing Images:</label>
            @if($roomType->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($roomType->images as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Room Image" class="w-full h-32 object-cover rounded">
                            <div class="absolute top-0 right-0 m-1">
                                <label for="delete_image_{{ $image->id }}" class="flex items-center bg-white p-1 rounded-full cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" id="delete_image_{{ $image->id }}" value="{{ $image->id }}" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    <span class="ml-1 text-xs text-red-600 font-bold">Del</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No images uploaded yet.</p>
            @endif
        </div>

        {{-- Add New Images --}}
        <div class="mb-6">
            <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Add New Images:</label>
            <input type="file" name="images[]" id="images" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" multiple accept="image/*">
            @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Room Type
            </button>
        </div>
    </form>
</div>
@endsection