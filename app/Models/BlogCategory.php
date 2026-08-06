<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use App\Support\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory, Sluggable, LogsModelActivity;

    protected $slugSource = 'name';

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
