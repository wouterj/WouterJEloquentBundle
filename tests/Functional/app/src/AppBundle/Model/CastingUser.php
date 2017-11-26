<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CastingUser extends User
{
    public $table = 'users';

    public $casts = [
        'name' => 'string',
        'date_of_birth' => 'date',
        'is_admin' => 'boolean'
    ];

    /**
     * @Assert\NotBlank(message="The username should not be blank.")
     */
    public function getName()
    {
        return $this->name;
    }

    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
}
