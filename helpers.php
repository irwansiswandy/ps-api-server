<?php

function format($data = null)
{
    return new App\Classes\Formatter($data);
}

function get($data = null)
{
    return new App\Classes\Getter($data);
}