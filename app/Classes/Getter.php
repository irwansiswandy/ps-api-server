<?php

namespace App\Classes;

class Getter
{
    protected $data;

    public function __construct($data = null)
    {
        if ($data)
        {
            $this->data = $data;
        }
    }

    public function userType()
    {
        switch ($this->data)
        {
            case 'user':
                return 'App\Models\User';
                break;
            case 'admin':
                return 'App\Models\Admin';
                break;
        }
    }

    public function guardName()
    {
        switch ($this->data)
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