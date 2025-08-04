<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Fetch room types, eager load the first image for each
        $roomTypes = RoomType::with('images')->take(3)->get();
        
        return view('public.home', compact('roomTypes'));
    }
}