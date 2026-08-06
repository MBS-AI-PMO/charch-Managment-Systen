<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TitheFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $fund) {
            if (! $fund->slug) {
                $fund->slug = Str::slug($fund->name);
            }
        });
    }

    public function tithes(): HasMany
    {
        return $this->hasMany(Tithe::class, 'fund_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
