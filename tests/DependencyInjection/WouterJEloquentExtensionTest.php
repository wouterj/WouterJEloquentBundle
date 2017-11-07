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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use WouterJ\EloquentBundle\EventListener\EloquentInitializer;
use WouterJ\EloquentBundle\Facade\Schema;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseManager;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
abstract class WouterJEloquentExtensionTest extends TestCase
{
    protected $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.root_dir', sys_get_temp_dir());
        $this->container->registerExtension(new WouterJEloquentExtension());
    }

    private function compile()
    {
        $this->container->compile();
    }

    private function load($config, $compile = true)
    {
        $this->loadConfig($this->container, $config);

        if ($compile) {
            $this->compile();
        }
    }

    abstract protected function loadConfig(ContainerBuilder $container, $name);

    private function assertContainerHasService($id, $class)
    {
        $this->assertTrue($this->container->has($id));
        $this->assertEquals($class, $this->container->findDefinition($id)->getClass());
    }

    /** @test */
    public function it_creates_capsule_with_connections()
    {
        $this->load('with_connections');

        $this->assertContainerHasService('wouterj_eloquent', Manager::class);
        $this->assertContainerHasService('wouterj_eloquent.database_manager', DatabaseManager::class);
        $this->assertFalse($this->container->has('wouterj_eloquent.initializer'));
        $this->assertEquals('connection_1', $this->container->getParameter('wouterj_eloquent.default_connection'));

        $connectionCalls = array_values(array_map(
            function ($c) { return $c[1]; },
            array_filter(
                $this->container->getDefinition('wouterj_eloquent')->getMethodCalls(),
                function ($c) { return $c[0] === 'addConnection'; }
            )
        ));
        $this->assertEquals([
            [[
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => null,
                'database' => 'db',
                'username' => 'root',
                'password' => null,
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => null
            ], 'default'],
            [[
                'driver' => 'sqlite',
                'host' => 'local',
                'port' => null,
                'database' => 'foo.db',
                'username' => 'user',
                'password' => 'pass',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => 'symfo_',
            ], 'connection_1'],
        ], $connectionCalls);
    }

    /** @test */
    public function it_can_enable_eloquent()
    {
        $this->load('eloquent_enabled');

        $this->assertContainerHasService('wouterj_eloquent.initializer', EloquentInitializer::class);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage At least one connection must be configured
     */
    public function it_requires_at_least_one_connection()
    {
        $this->load('no_connection');
    }

    /** @test */
    public function it_only_requires_a_database_option()
    {
        $this->load('only_required_options');

        $this->assertContainerHasService('wouterj_eloquent', Manager::class);
    }

    /** @test */
    public function it_can_enable_facade_aliases()
    {
        $this->load('with_aliases');

        $this->assertContainerHasService('wouterj_eloquent.aliases.loader', AliasesLoader::class);
        $this->assertEquals([['addAlias', ['Schema', Schema::class]]], $this->container->getDefinition('wouterj_eloquent.aliases.loader')->getMethodCalls());
    }

    /**
     * @test
     * @group legacy
     * @expectedDeprecation Driver name "postgres" is deprecated as of version 0.4 and will be removed in 1.0. Use "pgsql" instead.
     * @expectedDeprecation Driver name "sql server" is deprecated as of version 0.4 and will be removed in 1.0. Use "sqlsrv" instead.
     */
    public function it_notifies_and_aliases_deprecated_driver_names()
    {
        $this->load('deprecated_drivers');
    }
}
