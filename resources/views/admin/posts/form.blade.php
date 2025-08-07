@extends('layouts.admin')
@section('title', isset($post) ? 'Edit Post' : 'Create Post')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">
    {{ isset($post) ? 'Edit Post' : 'Create New Post' }}
</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ isset($post) ? route('admin.posts.update', $post->id) : route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($post))
            @method('PUT')
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-4">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('title', $post->title ?? '') }}" required>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt (Short Summary)</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                </div>

                {{-- Body --}}
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
                    <textarea name="body" id="body" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('body', $post->body ?? '') }}</textarea>
                </div>
            </div>

            <div class="md:col-span-1 space-y-4">
                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $post->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="draft" {{ old('status', $post->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                {{-- Published Date --}}
                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to publish immediately (if status is 'Published').</p>
                </div>

                {{-- Featured Image --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Featured Image</label>
                    <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @if(isset($post) && $post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="Current Image" class="mt-4 w-full h-32 object-cover rounded">
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                {{ isset($post) ? 'Update Post' : 'Save Post' }}
            </button>
        </div>
    </form>
</div>
@endsection