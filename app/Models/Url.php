<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $fillable = [
        'original_url',
        'short_code',
        'expires_at',
        'user_id',
        'count'
    ];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }
}
