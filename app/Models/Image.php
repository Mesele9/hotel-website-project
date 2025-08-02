<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
    ];

    /**
     * Get the parent imageable model (RoomType, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}