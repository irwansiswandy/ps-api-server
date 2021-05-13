<?php

namespace App\Classes;

class Formatter
{
    public function __construct()
    {
        //
    }

    public function response($message, $data = null)
    {
        return [
            'message' => $message,
            'data' => $data
        ];
    }

    public function accessToken(\App\Models\AccessToken $access_token)
    {
        return [
            'ACT' => $access_token->value,
            'SSK' => $access_token->session_key,
            'GDN' => getter()->guardName($access_token->access_tokenable_type),
            'UID' => $access_token->access_tokenable_id
         ];
    }
}