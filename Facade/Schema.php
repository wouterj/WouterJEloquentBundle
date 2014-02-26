<?php

namespace Wj\EloquentBundle\Facade;

class Schema extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return static::$container->get('wj_eloquent.database_manager')->getSchemaBuilder();
    }
}
