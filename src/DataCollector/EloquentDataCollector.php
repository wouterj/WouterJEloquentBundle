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
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EloquentDataCollector extends DataCollector
{
    /** @var Manager */
    private $capsule;
    /** @var QueryListener */
    private $queryListener;
    /** @var SymfonyVersion */
    private $symfonyVersion;


    public function __construct(Manager $capsule, QueryListener $queryListener)
    {
        $this->capsule = $capsule;
        $this->queryListener = $queryListener;
        $this->symfonyVersion = floatval(\Symfony\Component\HttpKernel\Kernel::VERSION);
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $connections = array_map(function ($config) {
            return ($this->symfonyVersion < 3.2) ? $this->varToString($config) : $this->cloneVar($config);
        }, $this->capsule->getContainer()['config']['database.connections']);

        $usedConnections = [];
        foreach (array_keys($this->capsule->getDatabaseManager()->getConnections()) as $name) {
            $usedConnections[$name] = $connections[$name];
        }

        $queries = $this->queryListener->getQueriesByConnection();
        foreach ($queries as $connectionName => $q) {
            foreach ($q as $i => $query) {
                if ($this->symfonyVersion < 3.2) {
                    $queries[$connectionName][$i]['bindings'] = $this->varToString($query['bindings']);
                } else {
                    $queries[$connectionName][$i]['bindings'] = $this->cloneVar($query['bindings']);
                }
            }
        }

        $this->data = [
            'connections' => $connections,
            'used_connections' => $usedConnections,
            'queries' => $queries,
        ];
    }

    public function reset()
    {
        $this->data = [];
    }

    public function connections()
    {
        return $this->data['connections'];
    }

    public function usedConnections()
    {
        return $this->data['used_connections'];
    }

    public function queryForConnection($name)
    {
        return $this->data['queries'][$name];
    }

    public function queries()
    {
        return count($this->data['queries']) ? call_user_func_array('array_merge', $this->data['queries']) : [];
    }

    public function getName()
    {
        return 'wouterj_eloquent.eloquent_collector';
    }
}
