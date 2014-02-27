<?php

namespace WouterJ\EloquentBundle\Facade;

class Db extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'wj_eloquent.database_manager';
    }
}