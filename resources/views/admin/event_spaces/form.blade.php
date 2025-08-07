@extends('layouts.admin')
@section('title', isset($space) ? 'Edit Event Space' : 'Create Event Space')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">
    {{ isset($space) ? 'Edit Event Space' : 'Create New Event Space' }}
</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ isset($space) ? route('admin.event-spaces.update', $space->id) : route('admin.event-spaces.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($space))
            @method('PUT')
        @endif
        
        <div class="space-y-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Space Name</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('name', $space->name ?? '') }}" required>
            </div>

            {{-- Capacity --}}
            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">Max Capacity (Guests)</label>
                <input type="number" name="capacity" id="capacity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('capacity', $space->capacity ?? '') }}" required min="1">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('description', $space->description ?? '') }}</textarea>
            </div>

            {{-- Featured Image --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Featured Image (Main Thumbnail)</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @if(isset($space) && $space->image)
                    <img src="{{ asset('storage/' . $space->image) }}" alt="Current Image" class="mt-4 w-48 h-32 object-cover rounded">
                @endif
            </div>

            {{-- ** NEW: GALLERY IMAGES ** --}}
            {{-- Existing Gallery Images --}}
            @if(isset($space) && $space->images->count() > 0)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Existing Gallery Images (Check to delete)</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                    @foreach($space->images as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Gallery Image" class="w-full h-32 object-cover rounded">
                            <div class="absolute top-0 right-0 m-1">
                                <label for="delete_image_{{ $image->id }}" class="flex items-center bg-white p-1 rounded-full cursor-pointer shadow">
                                    <input type="checkbox" name="delete_images[]" id="delete_image_{{ $image->id }}" value="{{ $image->id }}" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    <span class="ml-1 text-xs text-red-600 font-bold">Del</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Add New Gallery Images --}}
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Add New Gallery Images</label>
                <input type="file" name="images[]" id="images" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                {{ isset($space) ? 'Update Space' : 'Save Space' }}
            </button>
        </div>
    </form>
</div>
@endsection