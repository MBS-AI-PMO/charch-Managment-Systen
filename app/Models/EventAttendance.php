<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'user_id', 'guest_name', 'guest_count',
        'checked_in_at', 'method', 'recorded_by',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function displayName(): string
    {
        return $this->user?->name ?? ($this->guest_name ?: 'Guest');
    }
}
