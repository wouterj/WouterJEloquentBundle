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

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class EloquentDataCollector extends DataCollector
{
    /** @var Manager */
    private $capsule;
    /** @var QueryListener */
    private $queryListener;

    public function __construct(Manager $capsule, QueryListener $queryListener)
    {
        $this->capsule = $capsule;
        $this->queryListener = $queryListener;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $connections = array_map(function ($config) {
            return $this->cloneVar($config);
        }, $this->capsule->getContainer()->make('config')['database.connections']);

        $usedConnections = [];
        foreach (array_keys($this->capsule->getDatabaseManager()->getConnections()) as $name) {
            $usedConnections[$name] = $connections[$name];
        }

        $queries = $this->queryListener->getQueriesByConnection();
        foreach ($queries as $connectionName => $q) {
            foreach ($q as $i => $query) {
                $queries[$connectionName][$i]['bindings'] = $this->cloneVar($query['bindings']);
            }
        }

        $this->data = [
            'connections' => $connections,
            'used_connections' => $usedConnections,
            'queries' => $queries,
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    /** @return Data[] */
    public function connections(): array
    {
        return $this->data['connections'];
    }

    /** @return Data[] */
    public function usedConnections(): array
    {
        return $this->data['used_connections'];
    }

    public function queryForConnection(string $name): array
    {
        return $this->data['queries'][$name];
    }

    public function queries(): array
    {
        return count($this->data['queries']) ? call_user_func_array('array_merge', array_values($this->data['queries'])) : [];
    }

    public function getName(): string
    {
        return 'wouterj_eloquent.eloquent_collector';
    }
}
