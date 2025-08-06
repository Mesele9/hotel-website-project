<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the sluggable trait for a model.
     *
     * @return void
     */
    protected static function bootSluggable()
    {
        static::saving(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
