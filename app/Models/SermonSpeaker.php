<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Model;

class SermonSpeaker extends Model
{
    use Sluggable, LogsModelActivity;

    protected $slugSource = 'name';

    protected $guarded = [];

    public function sermons()
    {
        return $this->hasMany(Sermon::class, 'speaker_id');
    }
}
