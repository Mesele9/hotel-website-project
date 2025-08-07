@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
    <hr class="my-4">
    <p class="text-gray-600 mb-8">Welcome, {{ Auth::user()->name }}. Here is a summary of hotel activity.</p>
    
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- New Bookings Today -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-gray-500 text-sm font-medium uppercase tracking-wider">New Bookings (Today)</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $newBookingsCount }}</p>
        </div>

        <!-- Arrivals Today -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Arrivals (Today)</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $arrivalsCount }}</p>
        </div>

        <!-- Departures Today -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Departures (Today)</h2>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $departuresCount }}</p>
        </div>

        <!-- Occupancy Rate -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Occupancy (Next 7 Days)</h2>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $occupancyRate }}%</p>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Recent Bookings</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600">Reference</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600">Guest</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600">Room</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600">Check-in</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($recentBookings as $booking)
                    <tr>
                        <td class="text-left py-3 px-4 font-mono text-sm">{{ $booking->booking_reference }}</td>
                        <td class="text-left py-3 px-4">{{ $booking->guest_name }}</td>
                        <td class="text-left py-3 px-4">{{ $booking->room->roomType->name }} ({{ $booking->room->room_number }})</td>
                        <td class="text-left py-3 px-4">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                        <td class="text-left py-3 px-4">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No recent bookings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection