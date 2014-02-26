<?php

namespace Wj\EloquentBundle\Tests\Fixtures\Facade;

use Wj\EloquentBundle\Facade\Facade;

class ContainerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'facade_service';
    }
}
