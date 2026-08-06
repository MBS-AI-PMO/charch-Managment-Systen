<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'image_path',
        'eyebrow',
        'heading',
        'sub',
        'primary_cta_url',
        'primary_cta_label',
        'secondary_cta_url',
        'secondary_cta_label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('position')->orderBy('id');
    }
}
