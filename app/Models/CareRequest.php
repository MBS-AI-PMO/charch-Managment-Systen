<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareRequest extends Model
{
    use HasFactory, LogsModelActivity;

    protected $guarded = [];

    protected $casts = [
        'share_with_team' => 'bool',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }
}
