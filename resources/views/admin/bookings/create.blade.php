@extends('layouts.admin')

@section('title', 'Add New Booking')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Add New Booking</h1>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('admin.bookings.store') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Guest Info --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold border-b">Guest Details</h3>
                <div>
                    <label for="guest_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="guest_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            {{-- Booking Info --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold border-b">Booking Details</h3>
                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" id="room_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select a Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->roomType->name }} - Room {{ $room->room_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="check_in_date" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="check_out_date" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                 <div>
                    <label for="total_guests" class="block text-sm font-medium text-gray-700">Number of Guests</label>
                    <input type="number" name="total_guests" id="total_guests" value="{{ old('total_guests') }}" required min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes / Special Requests</label>
            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Create Booking
            </button>
        </div>
    </form>
</div>
@endsection