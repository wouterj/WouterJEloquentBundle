<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseManager;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use WouterJ\EloquentBundle\EventListener\EloquentInitializer;

class WouterJEloquentExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions()
    {
        return [new WouterJEloquentExtension()];
    }

    /** @test */
    public function it_creates_capsule_with_connections()
    {
        $this->load(['connections' => $this->getConnectionConfig()]);

        $this->assertContainerBuilderHasService('wouterj_eloquent', Manager::class);
        $this->assertContainerBuilderHasService('wouterj_eloquent.database_manager', DatabaseManager::class);
        $this->assertFalse($this->container->has('wouterj_eloquent.initializer'));
    }

    /** @test */
    public function it_can_enable_eloquent()
    {
        $this->load([
            'connections' => $this->getConnectionConfig(),
            'eloquent'    => ['enabled' => true],
        ]);

        $this->assertContainerBuilderHasService('wouterj_eloquent.initializer', EloquentInitializer::class);
    }

    /**
     * @test
     * @expectedException LogicException
     * @expectedExceptionMessage There should be at least one connection configured on "wouterj_eloquent.connections" in order to use the Eloquent ORM.
     */
    public function it_fails_to_enable_eloquent_without_connections()
    {
        $this->load(['eloquent' => ['enabled' => true]]);
    }

    protected function getConnectionConfig()
    {
        return [
            'default' => [
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'database'  => 'db',
                'username'  => 'root',
                'password'  => '',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ],
        ];
    }
}
