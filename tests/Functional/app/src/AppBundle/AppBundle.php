<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle;

use AppBundle\Model\User;
use AppBundle\Model\SoftDeleteUser;
use AppBundle\Model\UserObserver;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class AppBundle extends Bundle
{
    public function boot()
    {
        User::observe(UserObserver::class);
        SoftDeleteUser::observe(UserObserver::class);
    }
}
