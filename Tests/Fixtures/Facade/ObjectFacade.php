<?php

namespace Wj\EloquentBundle\Tests\Fixtures\Facade;

use Wj\EloquentBundle\Facade\Facade;

class ObjectFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new Dummy();
    }
}

class Dummy
{
    public function foo()
    { }
}
