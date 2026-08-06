<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerRequestPray extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['prayer_request_id', 'user_id'];
    protected $casts = ['created_at' => 'datetime'];

    public function prayerRequest()
    {
        return $this->belongsTo(PrayerRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
