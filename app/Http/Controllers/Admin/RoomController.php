<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('roomType')->latest('id')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|string|in:Available,Occupied,Under Maintenance',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Add a check for bookings in the future
        // if ($room->bookings()->where('check_out_date', '>', now())->exists()) {
        //     return redirect()->route('admin.rooms.index')->with('error', 'Cannot delete a room with active bookings.');
        // }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully.');
    }
}