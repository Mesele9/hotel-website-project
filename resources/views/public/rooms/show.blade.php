@extends('layouts.public')

@section('title', $roomType->name)

@section('content')
<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Image Gallery -->
            <div>
                <img id="main-image" src="{{ $roomType->images->first() ? asset('storage/' . $roomType->images->first()->path) : 'https://via.placeholder.com/800x600.png?text=No+Image' }}" alt="{{ $roomType->name }}" class="w-full h-96 object-cover rounded-lg shadow-lg">
                @if($roomType->images->count() > 1)
                <div class="grid grid-cols-5 gap-4 mt-4">
                    @foreach($roomType->images as $image)
                    <div>
                        <img src="{{ asset('storage/' . $image->path) }}" alt="Thumbnail" class="w-full h-24 object-cover rounded-md cursor-pointer thumbnail-image border-2 border-transparent hover:border-primary">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Description -->
            <div class="mt-8">
                <h1 class="text-4xl font-bold text-gray-800">{{ $roomType->name }}</h1>
                <p class="text-lg text-gray-600 mt-4">{{ $roomType->description }}</p>
            </div>

            <!-- Amenities -->
            <div class="mt-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b pb-2">Amenities</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                    @forelse($roomType->amenities as $amenity)
                        <div class="flex items-center text-gray-700">
                            {{-- We can add icons here later if they exist in the DB --}}
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>{{ $amenity->name }}</span>
                        </div>
                    @empty
                        <p>No specific amenities listed for this room.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Booking Widget Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-lg sticky top-8">
                <h2 class="text-2xl font-bold text-center mb-6">Book Your Stay</h2>
                <p class="text-center text-2xl font-bold text-primary mb-4">
                    ${{ number_format($roomType->base_price, 2) }}
                    <span class="text-sm font-normal text-gray-500">/ night</span>
                </p>
                <form action="{{ route('booking.search') }}" method="GET">
                    <div class="space-y-4">
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700">Check-in</label>
                            <input type="date" name="check_in_date" id="check_in_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="check_out_date" class="block text-sm font-medium text-gray-700">Check-out</label>
                            <input type="date" name="check_out_date" id="check_out_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700">Guests</label>
                            <input type="number" name="guests" id="guests" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="2" min="1" required>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-md hover:bg-primary-dark font-semibold">Check Availability</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple script for the image gallery
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            mainImage.src = this.src;
            // Optional: highlight the active thumbnail
            thumbnails.forEach(t => t.classList.remove('border-primary'));
            this.classList.add('border-primary');
        });
    });
</script>
@endpush
@endsection