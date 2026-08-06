<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
