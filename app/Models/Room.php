<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'room_number',
        'status',
    ];

    /**
     * The type of room (e.g., "Standard Queen").
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * All bookings associated with this specific room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * All manual date blocks associated with this specific room.
     */
    public function dateBlocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DateBlock::class);
    }

}