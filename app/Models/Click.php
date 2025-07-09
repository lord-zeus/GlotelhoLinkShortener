<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = [
        'url_id',
        'country',
        'city',
        'latitude',
        'longitude',
        'browser',
        'os',
        'device',
    ];

    public function url()
    {
        return $this->belongsTo(Url::class);
    }
}
