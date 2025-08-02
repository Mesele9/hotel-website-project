<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany; // <-- Add this


class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'base_price',
    ];

    /**
     * The physical rooms that are of this type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * The amenities that this room type has.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    /**
     * Get all of the room type's images.
     */
    public function images(): MorphMany // <-- Add this new method
    {
        return $this->morphMany(Image::class, 'imageable');
    }

}