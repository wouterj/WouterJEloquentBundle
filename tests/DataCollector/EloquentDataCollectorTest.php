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

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Fluent;
use WouterJ\EloquentBundle\MockeryTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class EloquentDataCollectorTest extends TestCase
{
    use MockeryTrait;

    private Container $container;
    private $capsule;
    private $queryListener;
    private $collector;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->instance('config', new Fluent(['database.connections' => []]));

        $this->capsule = \Mockery::mock(Manager::class);
        $this->capsule->allows()->getContainer()->andReturn($this->container);
        $this->capsule->allows()->getDatabaseManager()->andReturn(new class{
            public function getConnections() { return []; }
        })->byDefault();

        $this->queryListener = \Mockery::mock(QueryListener::class);
        $this->queryListener->allows()->getQueriesByConnection()->andReturn([])->byDefault();

        $this->collector = new EloquentDataCollector($this->capsule, $this->queryListener);
    }

    /** @test */
    public function it_collects_connections()
    {
        $this->container['config']['database.connections'] = [
            'db1' => ['db' => 'foobar'],
            'db2' => ['db' => 'something else']
        ];

        $this->capsule->allows()->getDatabaseManager()->andReturn(new class{
            public function getConnections() {
                return ['db2' => ['db' => 'foobar']];
            }
        });

        $this->collector->collect(new Request(), new Response());

        $this->assertCount(2, $this->collector->connections());
        $this->assertCount(1, $this->collector->usedConnections());
    }

    /** @test */
    public function it_collects_queries()
    {
        $this->queryListener->allows()->getQueriesByConnection()->andReturn([
            'db1' => [
                ['sql' => 'select * from posts', 'bindings' => []],
                ['sql' => 'insert into posts values (?, ?)', 'bindings' => ['title', 'body']],
            ],
            'db2' => [['sql' => 'select * from site_data', 'bindings' => []]],
        ]);

        $this->collector->collect(new Request(), new Response());

        $this->assertCount(3, $this->collector->queries());
        $this->assertCount(2, $this->collector->queryForConnection('db1'));
    }
}
