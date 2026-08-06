<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    use HasFactory, Sluggable, LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'bool',
        'downloads_enabled' => 'bool',
        'preached_on' => 'date',
    ];

    public function series()
    {
        return $this->belongsTo(SermonSeries::class, 'series_id');
    }

    public function speaker()
    {
        return $this->belongsTo(SermonSpeaker::class, 'speaker_id');
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }
}
