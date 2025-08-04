<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Step 1: Find available rooms based on dates and guest count.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        // Find room types that have the capacity
        $availableRoomTypes = RoomType::where('capacity', '>=', $validated['guests'])
            ->whereDoesntHave('rooms.bookings', function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>', $checkIn);
                });
            })
            ->whereDoesntHave('rooms.dateBlocks', function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('start_date', '<', $checkOut)
                      ->where('end_date', '>', $checkIn);
                });
            })
             ->whereHas('rooms', function($query) {
                $query->where('status', 'Available');
            })
            ->with('images')
            ->get();

        return view('public.booking.results', compact('availableRoomTypes'));
    }

    /**
     * Step 2: Show the booking form with details for a selected room.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date',
            'guests' => 'required|integer'
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        // Find one specific available room of the chosen type
        $room = Room::where('room_type_id', $validated['room_type_id'])
            ->where('status', 'Available')
            ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in_date', '<', $checkOut)->where('check_out_date', '>', $checkIn);
                });
            })
            ->whereDoesntHave('dateBlocks', function ($query) use ($checkIn, $checkOut) {
                 $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('start_date', '<', $checkOut)->where('end_date', '>', $checkIn);
                });
            })
            ->with('roomType.images')
            ->firstOrFail(); // Fails if no room is available, preventing double booking

        $numberOfNights = $checkIn->diffInDays($checkOut);
        $totalPrice = $numberOfNights * $room->roomType->base_price;

        return view('public.booking.create', compact('room', 'numberOfNights', 'totalPrice'));
    }

    /**
     * Step 3: Store the booking in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date',
            'total_guests' => 'required|integer',
            'total_price' => 'required|numeric',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Final check for availability to prevent race conditions
        $isAvailable = !Booking::where('room_id', $validated['room_id'])
            ->where(function ($q) use ($validated) {
                $q->where('check_in_date', '<', $validated['check_out_date'])
                  ->where('check_out_date', '>', $validated['check_in_date']);
            })->exists();

        if (!$isAvailable) {
            return back()->withErrors(['availability' => 'Sorry, this room was just booked. Please try again.'])->withInput();
        }

        $booking = DB::transaction(function () use ($validated) {
            $booking = new Booking($validated);
            $booking->booking_reference = 'YEG-' . strtoupper(Str::random(8));
            $booking->payment_status = 'Paid'; // Simulate successful payment
            $booking->save();

            // Send confirmation email to the guest
            Mail::to($booking->guest_email)->send(new BookingConfirmationMail($booking));
            
            // ToDo: Send notification email to admin in the future

            return $booking;
        });

        return redirect()->route('booking.success', ['booking' => $booking->id]);
    }

    /**
     * Step 4: Show the booking success page.
     */
    public function success(Booking $booking)
    {
        return view('public.booking.success', compact('booking'));
    }
}