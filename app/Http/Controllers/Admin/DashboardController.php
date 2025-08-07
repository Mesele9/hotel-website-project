<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $today = Carbon::today();

        // 1. Stat Card: New Bookings Today
        $newBookingsCount = Booking::whereDate('created_at', $today)->count();

        // 2. Stat Card: Today's Arrivals
        $arrivalsCount = Booking::whereDate('check_in_date', $today)->count();

        // 3. Stat Card: Today's Departures
        $departuresCount = Booking::whereDate('check_out_date', $today)->count();

        // 4. Occupancy Rate (Example for the next 7 days)
        // This is a simplified calculation. A real-world scenario would be more complex.
        $totalRooms = \App\Models\Room::count();
        $bookingsInNext7Days = Booking::where('check_in_date', '<=', $today->copy()->addDays(7))
                                    ->where('check_out_date', '>', $today)
                                    ->count();
        $occupancyRate = ($totalRooms > 0) ? ($bookingsInNext7Days / ($totalRooms * 7)) * 100 : 0;
        
        // 5. Recent Bookings List
        $recentBookings = Booking::with('room.roomType')
                                ->latest()
                                ->take(5)
                                ->get();
        
        return view('admin.dashboard', [
            'newBookingsCount' => $newBookingsCount,
            'arrivalsCount' => $arrivalsCount,
            'departuresCount' => $departuresCount,
            'occupancyRate' => round($occupancyRate),
            'recentBookings' => $recentBookings
        ]);
    }
}