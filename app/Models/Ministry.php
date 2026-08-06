<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    use HasFactory, Sluggable, LogsModelActivity;

    protected $slugSource = 'name';

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'bool',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }
}
