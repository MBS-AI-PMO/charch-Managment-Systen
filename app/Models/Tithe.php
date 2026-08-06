<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tithe extends Model
{
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'user_id', 'giver_name', 'fund_id', 'amount_cents',
        'received_at', 'method', 'reference', 'note', 'recorded_by',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'received_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(TitheFund::class, 'fund_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function giverDisplayName(): string
    {
        return $this->user?->name ?? ($this->giver_name ?: 'Anonymous');
    }
}
