<?php

namespace App\Classes;

class Formatter
{
    protected $data;

    public function __construct($data = null)
    {
        if ($data)
        {
            $this->data = $data;
        }
    }

    public function response($message, $data)
    {
        return [
            'message' => $message,
            'data' => $data
        ];
    }

    public function accessToken()
    {
        return [
            'ACT' => $this->data->value,
            'SSK' => $this->data->session_key,
            'GDN' => get($this->data->access_tokenable_type)->guardName(),
            'UID' => $this->data->access_tokenable_id
         ];
    }
}