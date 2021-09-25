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
 * @method static \Illuminate\Database\Schema\Builder create(string $table, \Closure $callback)
 * @method static \Illuminate\Database\Schema\Builder createDatabase(string $name)
 * @method static \Illuminate\Database\Schema\Builder disableForeignKeyConstraints()
 * @method static \Illuminate\Database\Schema\Builder drop(string $table)
 * @method static \Illuminate\Database\Schema\Builder dropDatabaseIfExists(string $name)
 * @method static \Illuminate\Database\Schema\Builder dropIfExists(string $table)
 * @method static \Illuminate\Database\Schema\Builder enableForeignKeyConstraints()
 * @method static \Illuminate\Database\Schema\Builder rename(string $from, string $to)
 * @method static \Illuminate\Database\Schema\Builder table(string $table, \Closure $callback)
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     */
    public static function connection(string $name): Builder
    {
        return static::$container->get('wouterj_eloquent.database_manager')->connection($name)->getSchemaBuilder();
    }

    /** @return object|string */
    protected static function getFacadeAccessor()
    {
        return static::$container->get('wouterj_eloquent.database_manager')->getSchemaBuilder();
    }
}
