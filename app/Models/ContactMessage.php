<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactMessage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'is_public' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ContactMessageReply::class)->oldest();
    }

    public function isReplied(): bool
    {
        return $this->replied_at !== null;
    }

    public function scopePublicAnswered($query)
    {
        return $query
            ->where('is_public', true)
            ->whereNotNull('replied_at')
            ->whereHas('replies', fn ($q) => $q->where('is_staff', true));
    }
}
