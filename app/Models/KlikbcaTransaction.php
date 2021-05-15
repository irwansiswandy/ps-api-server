<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlikbcaTransaction extends Model
{
    protected $fillable = [
        'date',
        'description',
        'flows'
    ];

    public function getDescriptionAttribute($description)
    {
        return json_decode($description, true);
    }
}