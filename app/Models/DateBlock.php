<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DateBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'start_date',
        'end_date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}