@extends('layouts.public')
@section('title', 'Local Guide')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="container mx-auto px-6 py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Local Guide</h1>
        <p class="text-gray-600 mt-2">Discover the best attractions, dining, and events our city has to offer.</p>
    </div>

    <!-- Posts Grid -->
    <div class="container mx-auto px-6 py-8">
        @if($posts->isEmpty())
            <p class="text-center text-lg text-gray-500">No articles have been published yet. Please check back soon!</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                    <a href="{{ route('post.show', $post->slug) }}">
                        <img src="{{ $post->image ? asset('storage/' . $post->image) : 'https://via.placeholder.com/400x300.png?text=YegeZulejoch' }}" alt="{{ $post->title }}" class="w-full h-56 object-cover hover:opacity-90 transition-opacity">
                    </a>
                    <div class="p-6 flex-1 flex flex-col">
                        <p class="text-sm text-gray-500">{{ $post->category->name }} &bull; {{ $post->published_at->format('M d, Y') }}</p>
                        <h2 class="text-2xl font-bold mt-2">
                            <a href="{{ route('post.show', $post->slug) }}" class="hover:text-primary">{{ $post->title }}</a>
                        </h2>
                        <p class="text-gray-700 mt-2 flex-1">{{ $post->excerpt }}</p>
                        <div class="mt-4">
                            <a href="{{ route('post.show', $post->slug) }}" class="font-semibold text-primary hover:underline">Read More &rarr;</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection