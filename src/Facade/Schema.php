<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Facade;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Schema extends Facade
{
    /** {@inheritDoc} */
    protected static function getFacadeAccessor()
    {
        return static::$container->get('wouterj_eloquent.database_manager')->getSchemaBuilder();
    }
}
