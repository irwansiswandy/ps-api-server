<?php

namespace App\Classes;

class DataFormatter
{
    public function __construct()
    {

    }

    public function accessToken(\App\Models\AccessToken $access_token)
    {
        return [
            'ACT' => $access_token->value,
            'SSK' => $access_token->session_key,
            'GDN' => getter()->guard($access_token->access_tokenable_type),
            'UID' => $access_token->access_tokenable_id
         ];
    }
}