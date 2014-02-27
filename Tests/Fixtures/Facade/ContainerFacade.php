<?php

namespace WouterJ\EloquentBundle\Tests\Fixtures\Facade;

use WouterJ\EloquentBundle\Facade\Facade;

class ContainerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'facade_service';
    }
}
