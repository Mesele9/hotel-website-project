<?php
namespace App\Models;

use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, Sluggable;
    protected $fillable = ['name', 'slug'];
    public function posts(): HasMany { return $this->hasMany(Post::class); }
}
