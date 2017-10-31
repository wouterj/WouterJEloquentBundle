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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class EloquentDataCollectorTest extends TestCase
{
    private $capsule;
    private $queryListener;
    private $collector;

    protected function setUp()
    {
        $this->capsule = $this->prophesize(Manager::class);
        $this->capsule->getContainer()->willReturn(['config' => ['database.connections' => []]]);
        $this->capsule->getDatabaseManager()->willReturn(new class{
            public function getConnections() { return []; }
        });

        $this->queryListener = $this->prophesize(QueryListener::class);
        $this->queryListener->getQueriesByConnection()->willReturn([]);

        $this->collector = new EloquentDataCollector($this->capsule->reveal(), $this->queryListener->reveal());
    }

    /** @test */
    public function it_collects_connections()
    {
        $this->capsule->getContainer()->willReturn([
            'config' => [
                'database.connections' => [
                    'db1' => ['db' => 'foobar'],
                    'db2' => ['db' => 'something else']
                ],
            ],
        ]);

        $this->capsule->getDatabaseManager()->willReturn(new class{
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
        $this->queryListener->getQueriesByConnection()->willReturn([
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
