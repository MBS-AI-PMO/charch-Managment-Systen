<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    use LogsModelActivity;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
