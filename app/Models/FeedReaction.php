<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedReaction extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['feed_post_id', 'user_id', 'kind'];
    protected $casts = ['created_at' => 'datetime'];

    public function feedPost()
    {
        return $this->belongsTo(FeedPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
