<x-mail::message>
# Your Booking is Confirmed!

Hello **{{ $booking->guest_name }}**,

Thank you for choosing {{ $settings['hotel_name'] ?? 'our hotel' }}. We are pleased to confirm your booking.

**Booking Reference:** {{ $booking->booking_reference }}

## Booking Details
- **Room Type:** {{ $booking->room->roomType->name }}
- **Check-in:** {{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }}
- **Check-out:** {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}
- **Total Guests:** {{ $booking->total_guests }}
- **Total Price:** ${{ number_format($booking->total_price, 2) }}

We look forward to welcoming you.

Thanks,<br>
{{ $settings['hotel_name'] ?? config('app.name') }}
</x-mail::message>