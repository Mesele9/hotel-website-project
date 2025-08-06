<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str; 
use App\Rules\EnsureRoomIsAvailable;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room.roomType']);

        // Handle text search for guest name, email, or booking reference
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('guest_name', 'like', "%{$searchTerm}%")
                  ->orWhere('guest_email', 'like', "%{$searchTerm}%")
                  ->orWhere('booking_reference', 'like', "%{$searchTerm}%");
            });
        }

        // Handle date range filter (for check-in dates)
        if ($request->filled('date_from')) {
            $query->whereDate('check_in_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in_date', '<=', $request->input('date_to'));
        }

        $bookings = $query->latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Eager load relationships for efficiency
        $booking->load(['room.roomType']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for creating a new resource.
    */
    public function create()
    {
        $rooms = Room::with('roomType')->where('status', 'Available')->get();
        return view('admin.bookings.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id', new EnsureRoomIsAvailable()],
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:255',
            'total_guests' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Calculate price
        $room = Room::with('roomType')->find($validated['room_id']);
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $numberOfNights = $checkIn->diffInDays($checkOut);
        
        $booking = new Booking($validated);
        $booking->total_price = $numberOfNights * $room->roomType->base_price;
        $booking->booking_reference = 'YEG-' . strtoupper(Str::random(8));
        $booking->payment_status = 'Paid'; // Assume manual bookings are paid
        $booking->save();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')->with('success', 'Booking cancelled successfully.');
    }
}
