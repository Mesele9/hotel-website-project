<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'total_guests',
        'total_price',
        'payment_status',
        'booking_reference',
        'notes',
    ];

    /**
     * The specific room that was booked.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}