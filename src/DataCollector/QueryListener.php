<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DataCollector;

use Illuminate\Database\Events\QueryExecuted;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class QueryListener
{
    private $queries = [];

    public function onQuery(QueryExecuted $event): void
    {
        if (!isset($this->queries[$event->connectionName])) {
            $this->queries[$event->connectionName] = [];
        }

        $this->queries[$event->connectionName][] = [
            'sql' => $event->sql,
            'time' => $event->time,
            'bindings' => $event->bindings,
            'connection' => $event->connectionName
        ];
    }

    public function getQueriesByConnection(): array
    {
        return $this->queries;
    }
}
