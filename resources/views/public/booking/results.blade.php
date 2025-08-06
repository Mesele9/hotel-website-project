@extends('layouts.public')
@section('title', 'Available Rooms')

@section('content')
<div class="container mx-auto px-6 py-16">
    <h1 class="text-4xl font-bold text-center mb-2">Available Rooms</h1>
    <p class="text-center text-gray-600 mb-12">For your stay from {{ \Carbon\Carbon::parse(request('check_in_date'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('check_out_date'))->format('M d, Y') }}</p>

    @php
        $cart = session()->get('booking_cart', []);
    @endphp

    @if(!empty($cart))
        <div class="bg-blue-100 border-t-4 border-blue-500 rounded-b text-blue-900 px-4 py-3 shadow-md mb-8" role="alert">
          <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
            <div>
              <p class="font-bold">Your booking has {{ count($cart) }} room(s).</p>
              <p class="text-sm">You can add more rooms or proceed to checkout.</p>
            </div>
            <div class="ml-auto pl-3">
                 <a href="{{ route('booking.cart.view') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    View Booking & Checkout
                </a>
            </div>
          </div>
        </div>
    @endif
    
    {{-- Feedback messages for add/error actions --}}
    @if(session('cart_success'))
        <div class="text-center text-green-700 bg-green-100 border border-green-400 p-4 rounded-lg mb-6">
            {{ session('cart_success') }}
        </div>
    @endif
    @if(session('cart_error'))
        <div class="text-center text-red-700 bg-red-100 border border-red-400 p-4 rounded-lg mb-6">
            {{ session('cart_error') }}
        </div>
    @endif


    @if($availableRoomTypes->isEmpty() && empty($cart))
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
                    <div class="p-6 flex flex-col justify-between flex-1">
                        <div>
                            <h3 class="text-3xl font-bold">{{ $roomType->name }}</h3>
                            <p class="text-gray-700 mt-2">{{ $roomType->description }}</p>
                            <p class="mt-4 text-sm text-gray-600">Max Guests: {{ $roomType->capacity }}</p>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <span class="text-2xl font-bold text-primary">${{ number_format($roomType->base_price, 2) }} <span class="text-sm font-normal text-gray-500">/ night</span></span>
                            <form action="{{ route('booking.cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                                <input type="hidden" name="check_in_date" value="{{ request('check_in_date') }}">
                                <input type="hidden" name="check_out_date" value="{{ request('check_out_date') }}">
                                <input type="hidden" name="guests" value="{{ request('guests') }}">
                                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark font-semibold">Add to Booking</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
