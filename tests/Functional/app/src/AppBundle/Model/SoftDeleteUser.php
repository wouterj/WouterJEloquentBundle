<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class SoftDeleteUser extends User
{
    use SoftDeletes;

    public $dates = ['deleted_at'];
    public $fillable = ['name', 'email', 'password'];
}
