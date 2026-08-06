<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory, Sluggable, LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'bool',
        'is_featured' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'attendance_open' => 'boolean',
        'reminded_at' => 'datetime',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeUpcoming($q)
    {
        return $q->where('starts_at', '>=', now());
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(\App\Models\EventRsvp::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(\App\Models\EventAttendance::class);
    }
}
