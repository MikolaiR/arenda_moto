<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Slug
{
    public static function bootSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = (string) Str::ulid();
            }
        });
 
        static::created(function ($model) {
            $model->slug = $model->id . '-' . $model->generateSlug();
            $model->saveQuietly();
        });
 
        static::updating(function ($model) {
            if ($model->isDirty('name') || $model->isDirty('year')) {
                $model->slug = $model->id . '-' . $model->generateSlug();
            }
        });
    }
 
    public function generateSlug(): string
    {
        return Str::slug($this->name) . '-' . $this->year;
    }
}
