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

                <h3 class="text-xl font-bold mb-4 mt-6">Payment Details</h3>
                <p class="text-gray-600 mb-4">This is a demo. No real payment will be processed.</p>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg text-lg">Confirm Booking & Pay</button>
            </form>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gray-50 p-8 rounded-lg shadow-inner">
            <h2 class="text-2xl font-bold mb-6">Booking Summary</h2>
            <div class="space-y-6">
                @foreach($cart as $item)
                <div class="flex items-start space-x-4">
                    <img src="{{ $item['room']->roomType->images->first() ? asset('storage/' . $item['room']->roomType->images->first()->path) : 'https://via.placeholder.com/100' }}" alt="{{ $item['room']->roomType->name }}" class="w-24 h-20 object-cover rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-semibold">{{ $item['room']->roomType->name }}</h4>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($item['check_in_date'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($item['check_out_date'])->format('M d, Y') }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $item['nights'] }} nights, {{ $item['guests'] }} guests</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">${{ number_format($item['price'], 2) }}</p>
                        <a href="{{ route('booking.cart.remove', $item['rowId']) }}" class="text-red-500 text-xs hover:underline">Remove</a>
                    </div>
                </div>
                @endforeach
            </div>
            <hr class="my-6">
            <div class="text-2xl font-bold text-right">
                <span class="text-gray-600">Total:</span>
                <span class="text-primary">${{ number_format($totalPrice, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
