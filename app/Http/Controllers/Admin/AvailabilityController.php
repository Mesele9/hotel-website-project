<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DateBlock;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
            $events[] = [ 'title' => 'Booked: ' . $booking->guest_name, 'start' => $booking->check_in_date, 'end' => Carbon::parse($booking->check_out_date)->addDay()->toDateString(), 'resourceId' => $booking->room_id, 'display' => 'background', 'color' => '#f87171' ];
        }

        $dateBlocks = DateBlock::all();
        foreach ($dateBlocks as $block) {
            $events[] = [ 'title' => 'Blocked', 'start' => $block->start_date, 'end' => Carbon::parse($block->end_date)->addDay()->toDateString(), 'resourceId' => $block->room_id, 'color' => '#fbbf24', 'editable' => false ];
        }

        return view('admin.availability.index', [ 'resources' => json_encode($resources), 'events' => json_encode($events) ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:block,unblock',
            'room_ids' => 'required|array',          // <-- Expect an array now
            'room_ids.*' => 'required|exists:rooms,id', // <-- Validate each ID
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->subDay();
        $roomIds = $request->room_ids;

        if ($request->action === 'block') {
            $dataToInsert = [];
            foreach ($roomIds as $roomId) {
                $dataToInsert[] = [
                    'room_id' => $roomId,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DateBlock::insert($dataToInsert); // Use bulk insert for efficiency

        } elseif ($request->action === 'unblock') {
            DateBlock::whereIn('room_id', $roomIds) // <-- Use whereIn for bulk action
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function($q) use ($startDate, $endDate) {
                                $q->where('start_date', '<', $startDate)
                                ->where('end_date', '>', $endDate);
                        });
                })
                ->delete();
        }

        return response()->json(['status' => 'success']);
    }
}