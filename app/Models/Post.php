<?php
namespace App\Models;

use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Post extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'excerpt', 
        'body', 'image', 'status', 'published_at'
    ];
    
    protected $casts = ['published_at' => 'datetime'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public function setPublishedAtAttribute($value)
    {
        $this->attributes['published_at'] = $value ? Carbon::parse($value) : null;
    }
}