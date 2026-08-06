<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Model;

class SermonSeries extends Model
{
    use Sluggable, LogsModelActivity;

    protected $table = 'sermon_series';

    protected $slugSource = 'name';

    protected $guarded = [];

    public function sermons()
    {
        return $this->hasMany(Sermon::class, 'series_id');
    }
}
