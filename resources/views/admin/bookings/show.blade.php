@extends('layouts.admin')

@section('title', 'Booking Details: ' . $booking->booking_reference)

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">Booking Details</h1>
    <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
        ← Back to All Bookings
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2 space-y-6">
        <!-- Guest Information -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Guest Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <p><strong>Name:</strong> {{ $booking->guest_name }}</p>
                <p><strong>Email:</strong> {{ $booking->guest_email }}</p>
                <p><strong>Phone:</strong> {{ $booking->guest_phone ?? 'N/A' }}</p>
                <p><strong>Total Guests:</strong> {{ $booking->total_guests }}</p>
            </div>
            @if($booking->notes)
            <div class="mt-4">
                <p><strong>Special Requests:</strong></p>
                <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
        <!-- Payment Information -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Payment Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <p><strong>Total Price:</strong> ${{ number_format($booking->total_price, 2) }}</p>
                <p><strong>Payment Status:</strong> 
                    <span class="px-2 py-1 font-semibold leading-tight rounded-sm
                        @if($booking->payment_status == 'Paid') bg-green-100 text-green-700
                        @elseif($booking->payment_status == 'Pending') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ $booking->payment_status }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    <!-- Booking Summary -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Booking Summary</h2>
        <div class="space-y-3">
            <p><strong>Reference:</strong><br><span class="font-mono">{{ $booking->booking_reference }}</span></p>
            <p><strong>Room:</strong><br>{{ $booking->room->roomType->name }} (No. {{ $booking->room->room_number }})</p>
            <p><strong>Check-in:</strong><br>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('D, M d, Y') }}</p>
            <p><strong>Check-out:</strong><br>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('D, M d, Y') }}</p>
            <p><strong>Booked On:</strong><br>{{ $booking->created_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="mt-6 border-t pt-4">
            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Cancel This Booking
                </button>
            </form>

        </div>
        </div>
    </div>
@endsection