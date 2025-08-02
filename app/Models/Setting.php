<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false; // We don't need created_at/updated_at for settings

    protected $fillable = [
        'key',
        'value',
    ];
}