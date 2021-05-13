<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $fillable = [
        'value',
        'session_key',
        'access_tokenable_id',
        'access_tokenable_type',
        'os',
        'browser',
        'device'
    ];

    public function access_tokenable()
    {
        $this->morphTo();
    }
}