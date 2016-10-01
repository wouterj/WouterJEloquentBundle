<?php

namespace AppBundle\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class User extends Model
{
    public $name;
    public $email;
    public $password;

    public $fillable = ['name', 'email', 'password'];
}
