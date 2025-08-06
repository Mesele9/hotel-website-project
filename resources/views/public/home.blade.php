@extends('layouts.public')

@section('title', 'Welcome to ' . ($settings['hotel_name'] ?? 'Our Hotel'))

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gray-800 text-white py-40 px-6 text-center">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        {{-- In a real project, you would replace this with a dynamic image from settings --}}
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop');"></div>
        <div class="relative">
            <h1 class="text-5xl font-bold mb-4">{{ $settings['hotel_name'] ?? 'Experience Unmatched Comfort' }}</h1>
            <p class="text-xl mb-8">Your perfect getaway awaits. Book your stay with us today.</p>
        </div>
    </div>

    <!-- Booking Widget Section (Placeholder) -->
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-6">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-center mb-6">Check Availability</h2>
                <form action="{{ route('booking.search') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                            <input type="date" id="check_in_date" name="check_in_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="check_out_date" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                            <input type="date" id="check_out_date" name="check_out_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700">Guests</label>
                            <input type="number" id="guests" name="guests" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="2" min="1" required>
                        </div>
                        <button type="submit" class="bg-primary text-white w-full py-2.5 rounded-md hover:bg-primary-dark">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Featured Rooms Section -->
    <div class="container mx-auto px-6 py-16">
        <h2 class="text-4xl font-bold text-center mb-12">Our Rooms & Suites</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($roomTypes as $roomType)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="{{ $roomType->images->first() ? asset('storage/' . $roomType->images->first()->path) : 'https://via.placeholder.com/400x300.png?text=No+Image' }}" alt="{{ $roomType->name }}" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $roomType->name }}</h3>
                        <p class="text-gray-700 mb-4">{{ Str::limit($roomType->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-primary">${{ number_format($roomType->base_price, 2) }} <span class="text-sm font-normal text-gray-500">/ night</span></span>
                            <a href="{{ route('rooms.show', $roomType->slug) }}" class="text-primary font-semibold hover:underline">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="md:col-span-3 text-center text-gray-500">No featured rooms available at the moment.</p>
            @endforelse
        </div>
    </div>

@endsection