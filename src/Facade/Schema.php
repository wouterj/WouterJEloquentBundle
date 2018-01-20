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

use Illuminate\Database\Schema\Builder;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string  $name
     *
     * @return Builder
     */
    public static function connection($name)
    {
        return static::$container->get('wouterj_eloquent.database_manager')->connection($name)->getSchemaBuilder();
    }

    /** {@inheritDoc} */
    protected static function getFacadeAccessor()
    {
        return static::$container->get('wouterj_eloquent.database_manager')->getSchemaBuilder();
    }
}
