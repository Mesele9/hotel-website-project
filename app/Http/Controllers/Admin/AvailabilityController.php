<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DateBlock;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Room;
use Illuminate\Validation\ValidationException;

class AvailabilityController extends Controller
{
    public function index()
    {
        // 1. Prepare resources for FullCalendar
        $roomTypes = RoomType::with('rooms')->get();
        $resources = [];
        foreach ($roomTypes as $type) {
            $children = [];
            foreach ($type->rooms as $room) {
                $children[] = [
                    'id' => $room->id,
                    'title' => 'Room ' . $room->room_number,
                    // Add the status to the resource object for the frontend
                    'extendedProps' => [
                        'status' => $room->status
                    ]
                ];
            }
            $resources[] = [ 'id' => 'type_' . $type->id, 'title' => $type->name, 'children' => $children ];
        }

        // 2. Prepare events for FullCalendar
        $events = [];

        // **FIX #1 LOGIC:** Add "Out of Service" blocks for non-available rooms
        foreach ($roomTypes as $type) {
            foreach ($type->rooms as $room) {
                if ($room->status !== 'Available') {
                    $events[] = [
                        'title' => $room->status,
                        'start' => '2020-01-01', // A date far in the past
                        'end' => '2030-01-01',   // A date far in the future
                        'resourceId' => $room->id,
                        'display' => 'background',
                        'color' => '#d1d5db' // Gray-300 for out of service
                    ];
                }
            }
        }

        $bookings = Booking::with('room')->get();
        foreach ($bookings as $booking) {
            $events[] = [
                'id'        => 'booking_' . $booking->id, // Add a unique ID for the event
                'title'     => 'Booked: ' . $booking->guest_name,
                'start'     => $booking->check_in_date,
                'end'       => Carbon::parse($booking->check_out_date)->addDay()->toDateString(),
                'resourceId'=> $booking->room_id,
                'display'   => 'background',
                'color'     => '#f87171', // Red-400 for bookings
                'classNames'=> ['booking-event'], // Add a CSS class to target these events
                'extendedProps' => [ // Add custom data here
                    'booking_id' => $booking->id,
                    'guest_name' => $booking->guest_name,
                    'reference' => $booking->booking_reference,
                ]
            ];

        }

        $dateBlocks = DateBlock::all();
        foreach ($dateBlocks as $block) {
            $events[] = [ 'title' => 'Blocked', 'start' => $block->start_date, 'end' => Carbon::parse($block->end_date)->addDay()->toDateString(), 'resourceId' => $block->room_id, 'color' => '#fbbf24', 'editable' => false ];
        }

        return view('admin.availability.index', [ 'resources' => json_encode($resources), 'events' => json_encode($events) ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:block,unblock',
            'room_ids' => 'required|array',
            'room_ids.*' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $requestedStart = Carbon::parse($validated['start_date']);
        $requestedEnd = Carbon::parse($validated['end_date'])->subDay();
        $roomIds = $validated['room_ids'];

        if ($validated['action'] === 'block') {
            foreach ($roomIds as $roomId) {
                $room = Room::find($roomId);

                if ($room->status !== 'Available') {
                    continue; // Skip rooms that are "Under Maintenance", etc.
                }

                // --- THE NEW "SMART SPLIT" LOGIC ---

                // 1. Define the total requested range.
                $gapsToBlock = [['start' => $requestedStart->copy(), 'end' => $requestedEnd->copy()]];

                // 2. Find all guest bookings that conflict with this range.
                $conflictingBookings = Booking::where('room_id', $roomId)
                    ->where('check_in_date', '<', $requestedEnd->copy()->addDay())
                    ->where('check_out_date', '>', $requestedStart)
                    ->orderBy('check_in_date')
                    ->get();
                
                // 3. "Cut out" the booking periods from our requested range.
                foreach ($conflictingBookings as $booking) {
                    $bookingStart = Carbon::parse($booking->check_in_date);
                    $bookingEnd = Carbon::parse($booking->check_out_date)->subDay(); // Bookings are inclusive
                    $nextGaps = [];
                    foreach ($gapsToBlock as $gap) {
                        // If the booking is entirely within this gap, it splits the gap into two.
                        if ($bookingStart->gt($gap['start']) && $bookingEnd->lt($gap['end'])) {
                            $nextGaps[] = ['start' => $gap['start'], 'end' => $bookingStart->copy()->subDay()];
                            $nextGaps[] = ['start' => $bookingEnd->copy()->addDay(), 'end' => $gap['end']];
                        }
                        // If the booking overlaps the start of the gap
                        elseif ($bookingEnd->gte($gap['start']) && $bookingEnd->lt($gap['end'])) {
                            $gap['start'] = $bookingEnd->copy()->addDay();
                            $nextGaps[] = $gap;
                        }
                        // If the booking overlaps the end of the gap
                        elseif ($bookingStart->gt($gap['start']) && $bookingStart->lte($gap['end'])) {
                            $gap['end'] = $bookingStart->copy()->subDay();
                            $nextGaps[] = $gap;
                        }
                        // If the booking completely covers the gap, the gap is removed.
                        elseif ($bookingStart->lte($gap['start']) && $bookingEnd->gte($gap['end'])) {
                            // Do nothing, this gap is fully booked.
                        }
                        else {
                            // The booking does not overlap with this gap.
                            $nextGaps[] = $gap;
                        }
                    }
                    $gapsToBlock = $nextGaps;
                }

                // 4. MERGE the newly calculated safe gaps with existing admin blocks.
                $existingBlocks = DateBlock::where('room_id', $roomId)
                    ->where('start_date', '<=', $requestedEnd)
                    ->where('end_date', '>=', $requestedStart)
                    ->get();

                $timeline = $gapsToBlock; // Start with the safe gaps
                foreach ($existingBlocks as $block) {
                    $timeline[] = ['start' => Carbon::parse($block->start_date), 'end' => Carbon::parse($block->end_date)];
                }
                
                // Delete old blocks to replace them with the new merged ones.
                $existingBlocks->each->delete();

                // Consolidate all ranges
                if (!empty($timeline)) {
                    // Remove any invalid ranges (where end is before start)
                    $timeline = array_filter($timeline, fn($range) => $range['end']->gte($range['start']));
                    if (empty($timeline)) continue;

                    usort($timeline, fn($a, $b) => $a['start'] <=> $b['start']);
                    $mergedTimeline = [$timeline[0]];
                    for ($i = 1; $i < count($timeline); $i++) {
                        $lastRange = &$mergedTimeline[count($mergedTimeline) - 1];
                        $currentRange = $timeline[$i];
                        if ($currentRange['start'] <= $lastRange['end']->copy()->addDay()) {
                            $lastRange['end'] = max($lastRange['end'], $currentRange['end']);
                        } else {
                            $mergedTimeline[] = $currentRange;
                        }
                    }

                    // 5. SAVE the new, clean, consolidated blocks.
                    foreach ($mergedTimeline as $blockRange) {
                        DateBlock::create([
                            'room_id' => $roomId,
                            'start_date' => $blockRange['start']->toDateString(),
                            'end_date' => $blockRange['end']->toDateString(),
                        ]);
                    }
                }
            }
        } elseif ($validated['action'] === 'unblock') {
            DateBlock::whereIn('room_id', $roomIds)
                ->where(function ($query) use ($requestedStart, $requestedEnd) {
                    $query->whereBetween('start_date', [$requestedStart, $requestedEnd])
                        ->orWhereBetween('end_date', [$requestedStart, $requestedEnd])
                        ->orWhere(function($q) use ($requestedStart, $requestedEnd) {
                                $q->where('start_date', '<', $requestedStart)
                                ->where('end_date', '>', $requestedEnd);
                        });
                })
                ->delete();
        }

        return response()->json(['status' => 'success']);
    }

}