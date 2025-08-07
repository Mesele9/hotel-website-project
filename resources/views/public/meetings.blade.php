@extends('layouts.public')
@section('title', 'Meetings & Events')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="container mx-auto px-6 py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Meetings & Events</h1>
        <p class="text-gray-600 mt-2">Host your next successful event in our versatile and elegant spaces.</p>
    </div>

    <!-- Event Spaces Grid -->
    <div class="container mx-auto px-6 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Our Venues</h2>
        @if($eventSpaces->isEmpty())
            <p class="text-center text-lg text-gray-500">Venue information is not available at this time. Please contact us for details.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($eventSpaces as $space)
                    <a href="{{ route('event_space.show', $space->slug) }}" class="block bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="{{ $space->image ? asset('storage/' . $space->image) : 'https://via.placeholder.com/400x300.png?text=Venue' }}" alt="{{ $space->name }}" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold mb-2">{{ $space->name }}</h3>
                            <p class="text-gray-500 mb-4">Up to {{ $space->capacity }} guests</p>
                            <p class="text-gray-700">{{ Str::limit($space->description, 100) }}</p>
                            <span class="mt-4 inline-block text-primary font-semibold">View Details &rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection