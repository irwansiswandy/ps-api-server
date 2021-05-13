<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationToken extends Model
{
    protected $fillable = [
        'value',
        'type',
        'activation_tokenable_id',
        'activation_tokenable_type'
    ];

    public function activation_tokenable()
    {
        $this->morphTo();
    }
}