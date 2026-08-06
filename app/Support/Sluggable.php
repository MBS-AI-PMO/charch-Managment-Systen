<?php

namespace App\Support;

use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable(): void
    {
        static::saving(function ($model) {
            if (empty($model->slug) && !empty($model->{$model->getSlugSource()})) {
                $base = Str::slug($model->{$model->getSlugSource()});
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function getSlugSource(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'title';
    }
}
