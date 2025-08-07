<?php

namespace App\Http\Controllers;

use Chapa\Chapa\Facades\Chapa as Chapa; 
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Rules\EnsureRoomIsAvailable;
use Illuminate\Support\Facades\Log; // **<-- THIS IS THE FIX**
use Illuminate\Support\Facades\Http;


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

        // **THE CORRECTED LOGIC**
        $availableRoomTypes = RoomType::query()
            // First, ensure the room type can accommodate the number of guests
            ->where('capacity', '>=', $validated['guests'])
            // Now, check if this room type HAS at least one room that satisfies ALL availability conditions
            ->whereHas('rooms', function ($roomQuery) use ($checkIn, $checkOut) {
                $roomQuery
                    // Condition 1: The room's master status must be 'Available'
                    ->where('status', 'Available')
                    // Condition 2: The room must NOT have any overlapping bookings
                    ->whereDoesntHave('bookings', function ($bookingQuery) use ($checkIn, $checkOut) {
                        $bookingQuery->where(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<', $checkOut)
                            ->where('check_out_date', '>', $checkIn);
                        });
                    })
                    // Condition 3: The room must NOT have any overlapping admin blocks
                    ->whereDoesntHave('dateBlocks', function ($blockQuery) use ($checkIn, $checkOut) {
                        $blockQuery->where(function ($q) use ($checkIn, $checkOut) {
                            $q->where('start_date', '<', $checkOut)
                            ->where('end_date', '>=', $checkIn);
                        });
                    });
            })
            ->with('images')
            ->get();
            
        return view('public.booking.results', compact('availableRoomTypes'));
    }

    /**
     * Add a selected room to the booking cart in the session.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date',
            'guests' => 'required|integer'
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        
        // **NEW LOGIC: Find an available room NOT already in the cart**
        $cart = session()->get('booking_cart', []);
        $bookedRoomIds = array_column($cart, 'room_id');

        // Find an available room of the selected type that is not already in the cart
        $room = Room::where('room_type_id', $validated['room_type_id'])
            ->where('status', 'Available')
            ->whereNotIn('id', $bookedRoomIds) // <-- Exclude rooms already in cart
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)->where('check_out_date', '>', $checkIn);
            })
            ->whereDoesntHave('dateBlocks', function ($q) use ($checkIn, $checkOut) {
                $q->where('start_date', '<', $checkOut)->where('end_date', '>=', $checkIn);
            })
            ->first();

        // Handle different scenarios
        if (!$room) {
            return back()->with('cart_error', 'Sorry, all available rooms of that type have been added to your booking or are no longer available.');
        }
        
        $rowId = uniqid(); // Generate a unique ID for this cart item

        $cart[$rowId] = [
            'rowId' => $rowId,
            'room_id' => $room->id,
            'room_type_id' => $room->room_type_id,
            'room' => $room->load('roomType.images'),
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'guests' => $validated['guests'],
        ];

        session()->put('booking_cart', $cart);

        // Use the redirect's session flash, which is more reliable
        return redirect()->route('booking.search', $request->query())
            ->with('cart_success', $room->roomType->name . ' has been added to your booking.');
    }


    /**
     * Remove an item from the booking cart.
     */
    public function removeFromCart($rowId)
    {
        $cart = session()->get('booking_cart', []);
        if (isset($cart[$rowId])) {
            unset($cart[$rowId]);
            session()->put('booking_cart', $cart);
        }
        return redirect()->route('booking.cart.view')->with('cart_success', 'Room removed from your booking.');
    }

    /**
     * Clear the entire booking cart.
     */
    public function clearCart()
    {
        session()->forget('booking_cart');
        return redirect()->route('home')->with('cart_success', 'Your booking has been cleared.');
    }

    /**
     * Show the booking cart / checkout page.
     */
    public function viewCart(Request $request)
    {
        $cart = session()->get('booking_cart', []);
        $totalPrice = 0;
        
        if (empty($cart)) {
            // If the cart is empty but we have search query, redirect to search.
            if ($request->has('check_in_date')) {
                 return redirect()->route('booking.search', $request->query());
            }
            return redirect()->route('home');
        }

        foreach ($cart as &$item) { // Pass by reference to add data
            $checkIn = Carbon::parse($item['check_in_date']);
            $checkOut = Carbon::parse($item['check_out_date']);
            $item['nights'] = $checkIn->diffInDays($checkOut);
            $item['price'] = $item['nights'] * $item['room']->roomType->base_price;
            $totalPrice += $item['price'];
        }

        return view('public.booking.checkout', [
            'cart' => $cart,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     * Step 3: Initialize Chapa payment and redirect the user.
     */
    public function initializeChapa(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
    
        $cart = session()->get('booking_cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->withErrors(['cart_error' => 'Your booking cart is empty.']);
        }
        
        $totalPrice = 0;
        foreach ($cart as $item) {
            $checkIn = Carbon::parse($item['check_in_date']);
            $checkOut = Carbon::parse($item['check_out_date']);
            $nights = $checkIn->diffInDays($checkOut);
            $totalPrice += (float)($nights * $item['room']->roomType->base_price);
        }
    
        $reference = 'YEG-TX-' . uniqid();
    
        $bookingDataForSession = [
            'guest_details' => $validated,
            'cart' => $cart,
            'total_price' => $totalPrice
        ];
        session()->put($reference, $bookingDataForSession);
    
        // Prepare the exact same data payload
        $data = [
            'amount' => $totalPrice,
            'email' => $validated['guest_email'],
            'tx_ref' => $reference,
            'currency' => 'ETB',
            'callback_url' => route('booking.callback'),
            'return_url' => route('booking.callback'),
            'first_name' => $validated['guest_name'],
            'last_name' => 'Guest', // Using a placeholder for required field
            "customization" => [
                "title" => "Hotel Payment",
                "description" => "Payment for your stay at YegeZulejoch Hotel"
            ]
        ];
    
        // **START: THE NEW MANUAL HTTP REQUEST LOGIC**
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY'),
                'Content-Type' => 'application/json'
            ])
            ->post('https://api.chapa.co/v1/transaction/initialize', $data);
    
            $payment = $response->json();
    
            if (isset($payment['status']) && $payment['status'] == 'success') {
                // Success, redirect to Chapa's checkout URL
                return redirect($payment['data']['checkout_url']);
            } else {
                // The API call was made, but Chapa returned an error
                Log::error('Chapa Initialization Failed (Manual HTTP): ', $payment);
                $errorMessage = $payment['message'] ?? 'Could not connect to payment gateway.';
                return redirect()->route('booking.cart.view')->withErrors(['error' => $errorMessage]);
            }
    
        } catch (\Exception $e) {
            // A lower-level connection error occurred (e.g., cURL error)
            Log::error('Chapa Connection Exception: ' . $e->getMessage());
            return redirect()->route('booking.cart.view')->withErrors(['error' => 'A connection error occurred. Please try again later.']);
        }
        // **END: THE NEW MANUAL HTTP REQUEST LOGIC**
    }

    /**
     * Step 4: Handle the callback from Chapa after payment attempt.
     */
    public function chapaCallback(Request $request)
    {
        $tx_ref = $request->query('tx_ref');

        // 1. Verify the transaction with Chapa's server
        $verification = Chapa::verifyPayment($tx_ref);

        if ($verification['status'] !== 'success') {
            // Handle failed or pending verification
            Log::warning('Chapa Payment Verification Failed or Pending: ', $verification);
            return redirect()->route('booking.cart.view')->withErrors(['error' => 'Payment verification failed. Please contact us if you have been charged.']);
        }

        // 2. Retrieve the booking data from the session
        $bookingData = session()->get($tx_ref);
        if (!$bookingData) {
            Log::error('No pending booking data found in session for tx_ref: ' . $tx_ref);
            // This might happen if the user has already completed this booking and refreshes the page
            return redirect()->route('home')->withErrors(['error' => 'Your session has expired or the booking is already processed.']);
        }

        // 3. Create the booking(s) in the database
        $bookings = DB::transaction(function () use ($bookingData) {
            // ... (The logic to create bookings from $bookingData is identical to the Stripe version) ...
            $createdBookings = [];
            foreach ($bookingData['cart'] as $item) {
                // Final availability check for each room
                $isAvailable = !Booking::where('room_id', $item['room_id'])->where(function ($q) use ($item) {
                    $q->where('check_in_date', '<', $item['check_out_date'])->where('check_out_date', '>', $item['check_in_date']);
                })->exists();
                if (!$isAvailable) { throw new \Exception('Sorry, room ' . $item['room']->room_number . ' was just booked.'); }
                
                $booking = new Booking();
                $booking->fill($bookingData['guest_details']);
                $booking->room_id = $item['room_id'];
                $booking->check_in_date = $item['check_in_date'];
                $booking->check_out_date = $item['check_out_date'];
                $booking->total_guests = $item['guests'];
                $checkIn = Carbon::parse($item['check_in_date']);
                $checkOut = Carbon::parse($item['check_out_date']);
                $nights = $checkIn->diffInDays($checkOut);
                $booking->total_price = $nights * Room::find($item['room_id'])->roomType->base_price;
                $booking->booking_reference = 'YEG-' . strtoupper(Str::random(8));
                $booking->payment_status = 'Paid';
                $booking->save();
                $createdBookings[] = $booking;
            }
            Mail::to($bookingData['guest_details']['guest_email'])->send(new BookingConfirmationMail($createdBookings[0]));
            return $createdBookings;
        });

        // 4. Clear all used session data
        session()->forget('booking_cart');
        session()->forget($tx_ref);

        $bookingIds = collect($bookings)->pluck('id')->toArray();
        return redirect()->route('booking.confirmation', ['booking_ids' => $bookingIds]);
    }
    
    /**
     * The final success/confirmation page view.
     */
    public function confirmation(Request $request)
    {
        // This is the old 'success' method, now renamed
        $validated = $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id'
        ]);
        $bookings = Booking::with('room.roomType')->whereIn('id', $validated['booking_ids'])->get();
        if ($bookings->isEmpty()) {
            return redirect()->route('home')->withErrors('Could not find booking confirmation.');
        }
        return view('public.booking.success', compact('bookings'));
    }


}