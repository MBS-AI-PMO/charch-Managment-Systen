<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrayerRequest extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'is_public' => 'bool',
        'is_anonymous' => 'bool',
        'pray_count' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function prays()
    {
        return $this->hasMany(PrayerRequestPray::class);
    }

    public function scopePublic(Builder $q): Builder
    {
        return $q->where('is_public', true);
    }

    public function displayName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : ($this->name ?: 'A church member');
    }
}
