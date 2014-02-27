<?php

namespace WouterJ\EloquentBundle\Tests\Fixtures\Facade;

use WouterJ\EloquentBundle\Facade\Facade;

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
