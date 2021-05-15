<?php

namespace App\Classes;

use Nahid\JsonQ\Jsonq;

class JsonQuery
{
    protected $file;

    public function __construct($file)
    {
        return new Jsonq($file);
    }
}