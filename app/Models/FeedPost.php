<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedPost extends Model
{
    use HasFactory, LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'pinned' => 'bool',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reactions()
    {
        return $this->hasMany(FeedReaction::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('published_at', '<=', now());
    }

    public function reactionCountsByKind(): array
    {
        return $this->reactions()
            ->selectRaw('kind, COUNT(*) as count')
            ->groupBy('kind')
            ->pluck('count', 'kind')
            ->toArray();
    }
}
