<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Tests\Fixtures\Facade;

use WouterJ\EloquentBundle\Facade\Facade;

class ContainerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'facade_service';
    }
}
