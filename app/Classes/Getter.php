<?php

namespace App\Classes;

class Getter
{
    public function __construct()
    {
        //
    }

    public function userType($guard)
    {
        switch ($guard)
        {
            case 'user':
                return 'App\Models\User';
                break;
            case 'admin':
                return 'App\Models\Admin';
                break;
        }
    }

    public function guardName($user_type)
    {
        switch ($user_type)
        {
            case 'App\Models\User':
                return 'user';
                break;
            case 'App\Models\Admin':
                return 'admin';
                break;
        }
    }
}