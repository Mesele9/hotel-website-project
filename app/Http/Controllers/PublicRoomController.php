<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class PublicRoomController extends Controller
{
    /**
     * Display the specified room type.
     */
    public function show(RoomType $roomType)
    {
        // Eager load images and amenities for efficiency
        $roomType->load('images', 'amenities');

        return view('public.rooms.show', compact('roomType'));
    }
}