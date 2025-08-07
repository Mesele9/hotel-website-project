@extends('layouts.public')
@section('title', $post->title)

@push('styles')
{{-- Basic styling for rendered markdown/rich text --}}
<style>
    .post-body h2 { font-size: 1.5em; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.5rem; }
    .post-body h3 { font-size: 1.25em; font-weight: bold; margin-top: 1.25rem; margin-bottom: 0.5rem; }
    .post-body p { margin-bottom: 1rem; line-height: 1.75; }
    .post-body ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
    .post-body ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
    .post-body a { color: var(--primary-color); text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="bg-white py-16">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Post Header -->
            <div class="text-center">
                <p class="text-primary font-semibold">{{ $post->category->name }}</p>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">{{ $post->title }}</h1>
                <p class="text-gray-500 mt-4">Published on {{ $post->published_at->format('F d, Y') }} by {{ $post->author->name }}</p>
            </div>

            <!-- Featured Image -->
            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-96 object-cover rounded-lg shadow-lg my-8">
            @endif

            <!-- Post Body -->
            <div class="mt-8 text-lg text-gray-700 post-body">
                {{-- Use nl2br and the Blade {!! !!} syntax to render basic line breaks.
                     For a real app, a Markdown parser like Parsedown would be better. --}}
                {!! nl2br(e($post->body)) !!}
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('page.local_guide') }}" class="text-primary font-semibold hover:underline">&larr; Back to Local Guide</a>
            </div>
        </div>
    </div>
</div>
@endsection
