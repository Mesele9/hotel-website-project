@extends('layouts.admin')

@section('title', 'All Bookings')

@section('content')
<div class="flex justify-between items-center my-4">
    <h1 class="text-3xl font-bold text-gray-800">Manage Bookings</h1>
    <a href="{{ route('admin.bookings.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
        + Add New Booking
    </a>
</div>

<!-- Search and Filter Form -->
<div class="bg-white shadow-md rounded-lg p-4 mb-6">
    <form action="{{ route('admin.bookings.index') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" class="border p-2 rounded w-full" placeholder="Search Guest, Email or Ref..." value="{{ request('search') }}">
            <input type="date" name="date_from" class="border p-2 rounded w-full" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="border p-2 rounded w-full" value="{{ request('date_to') }}">
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Reset</a>
            </div>
        </div>
    </form>
</div>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Reference</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Guest Name</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Room</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Check-in</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Check-out</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($bookings as $booking)
            <tr>
                <td class="text-left py-3 px-4 font-mono">{{ $booking->booking_reference }}</td>
                <td class="text-left py-3 px-4">{{ $booking->guest_name }}</td>
                <td class="text-left py-3 px-4">{{ $booking->room->roomType->name }} ({{ $booking->room->room_number }})</td>
                <td class="text-left py-3 px-4">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                <td class="text-left py-3 px-4">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                <td class="text-left py-3 px-4">
                    <span class="px-2 py-1 font-semibold leading-tight rounded-sm
                        @if($booking->payment_status == 'Paid') bg-green-100 text-green-700
                        @elseif($booking->payment_status == 'Pending') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700 @endif">
                        {{ $booking->payment_status }}
                    </span>
                </td>
                <td class="text-left py-3 px-4">
                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4">No bookings found matching your criteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $bookings->appends(request()->query())->links() }}
</div>
@endsection