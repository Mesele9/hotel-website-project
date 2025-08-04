@extends('layouts.public')
@section('title', 'Complete Your Booking')

@section('content')
<div class="container mx-auto px-6 py-16">
    <h1 class="text-4xl font-bold text-center mb-12">Complete Your Booking</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Guest Details Form -->
        <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6">Guest Information</h2>
            <form action="{{ route('booking.store') }}" method="POST">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="check_in_date" value="{{ request('check_in_date') }}">
                <input type="hidden" name="check_out_date" value="{{ request('check_out_date') }}">
                <input type="hidden" name="total_guests" value="{{ request('guests') }}">
                <input type="hidden" name="total_price" value="{{ $totalPrice }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="guest_name" class="block text-gray-700 text-sm font-bold mb-2">Full Name:</label>
                        <input type="text" name="guest_name" id="guest_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>
                    <div class="mb-4">
                        <label for="guest_email" class="block text-gray-700 text-sm font-bold mb-2">Email Address:</label>
                        <input type="email" name="guest_email" id="guest_email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="guest_phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number:</label>
                    <input type="tel" name="guest_phone" id="guest_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Special Requests:</label>
                    <textarea name="notes" id="notes" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
                </div>

                <h3 class="text-xl font-bold mb-4">Payment Details</h3>
                <p class="text-gray-600 mb-4">This is a demo. No real payment will be processed. Click confirm to simulate a successful booking.</p>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg text-lg">Confirm Booking</button>
            </form>
        </div>
        <!-- Booking Summary -->
        <div class="bg-gray-50 p-8 rounded-lg shadow-inner">
            <h2 class="text-2xl font-bold mb-6">Booking Summary</h2>
            <img src="{{ $room->roomType->images->first() ? asset('storage/' . $room->roomType->images->first()->path) : 'https://via.placeholder.com/400x300.png?text=No+Image' }}" alt="{{ $room->roomType->name }}" class="w-full h-48 object-cover rounded-lg mb-4">
            <h3 class="text-xl font-semibold">{{ $room->roomType->name }}</h3>
            <p class="text-gray-600">Room {{ $room->room_number }}</p>
            <hr class="my-4">
            <div class="space-y-2">
                <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse(request('check_in_date'))->format('D, M d, Y') }}</p>
                <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse(request('check_out_date'))->format('D, M d, Y') }}</p>
                <p><strong>Guests:</strong> {{ request('guests') }}</p>
                <p><strong>Nights:</strong> {{ $numberOfNights }}</p>
            </div>
            <hr class="my-4">
            <div class="text-2xl font-bold text-right">
                <span class="text-gray-600">Total:</span>
                <span class="text-primary">${{ number_format($totalPrice, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection