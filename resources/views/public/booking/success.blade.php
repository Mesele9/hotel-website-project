@extends('layouts.public')
@section('title', 'Booking Confirmed!')

@section('content')
<div class="container mx-auto px-6 py-16">
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-8 rounded-lg shadow-lg text-center">
        <h1 class="text-4xl font-bold mb-4">Thank You! Your Booking is Confirmed.</h1>
        <p class="text-lg mb-6">An email confirmation has been sent to you. Your booking references are listed below.</p>
        
        <div class="mt-8 text-left max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md space-y-6">
             <h3 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-3">Your Booking Details:</h3>

             {{-- **THE FIX: Loop through all confirmed bookings** --}}
             @foreach($bookings as $booking)
                 <div class="border-b pb-4 last:border-b-0">
                    <p class="text-lg"><strong>Booking Reference:</strong> <span class="font-mono text-primary">{{ $booking->booking_reference }}</span></p>
                    <p><strong>Guest:</strong> {{ $booking->guest_name }}</p>
                    <p><strong>Room:</strong> {{ $booking->room->roomType->name }} (Room {{ $booking->room->room_number }})</p>
                    <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('D, M d, Y') }}</p>
                    <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('D, M d, Y') }}</p>
                 </div>
             @endforeach
        </div>

        <a href="{{ route('home') }}" class="mt-8 inline-block bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary-dark">Back to Homepage</a>
    </div>
</div>
@endsection