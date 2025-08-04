@extends('layouts.public')
@section('title', 'Available Rooms')

@section('content')
<div class="container mx-auto px-6 py-16">
    <h1 class="text-4xl font-bold text-center mb-2">Available Rooms</h1>
    <p class="text-center text-gray-600 mb-12">For your stay from {{ \Carbon\Carbon::parse(request('check_in_date'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('check_out_date'))->format('M d, Y') }}</p>

    @if($availableRoomTypes->isEmpty())
        <div class="text-center text-red-500 bg-red-100 border border-red-400 p-6 rounded-lg">
            <h2 class="text-2xl font-bold">No Rooms Available</h2>
            <p class="mt-2">We're sorry, but no rooms are available for the selected dates or guest count. Please try different dates.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark">Back to Homepage</a>
        </div>
    @else
        <div class="space-y-8">
            @foreach($availableRoomTypes as $roomType)
                <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="{{ $roomType->images->first() ? asset('storage/' . $roomType->images->first()->path) : 'https://via.placeholder.com/400x300.png?text=No+Image' }}" alt="{{ $roomType->name }}" class="w-full md:w-1/3 h-64 object-cover">
                    <div class="p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-3xl font-bold">{{ $roomType->name }}</h3>
                            <p class="text-gray-700 mt-2">{{ $roomType->description }}</p>
                            <p class="mt-4 text-sm text-gray-600">Max Guests: {{ $roomType->capacity }}</p>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">${{ number_format($roomType->base_price, 2) }} <span class="text-sm font-normal text-gray-500">/ night</span></span>
                            <a href="{{ route('booking.create', ['room_type_id' => $roomType->id] + request()->query()) }}" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark font-semibold">Book This Room</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection