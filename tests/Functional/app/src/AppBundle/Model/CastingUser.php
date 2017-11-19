<?php

namespace AppBundle\Model;

class CastingUser extends User
{
    public $table = 'users';

    public $casts = [
        'name' => 'string',
        'date_of_birth' => 'date',
        'is_admin' => 'boolean'
    ];
}
