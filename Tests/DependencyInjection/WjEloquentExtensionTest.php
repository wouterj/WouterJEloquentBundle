<?php

namespace Wj\EloquentBundle\Tests\DependencyInjection;

use Wj\EloquentBundle\DependencyInjection\WjEloquentExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Illuminate\Database\Eloquent\Model as Eloquent;

class WjEloquentExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions()
    {
        return array(new WjEloquentExtension());
    }

    /** @test */
    public function it_disables_both_without_configuration()
    {
        $this->load();

        $this->assertCount(2, $this->container->getDefinitions());
    }

    /** @test */
    public function it_creates_capsule_with_connections()
    {
        $this->load(array('connections' => $this->getConnectionConfig()));

        $this->assertContainerBuilderHasService('wj_eloquent', '%wj_eloquent.class%');
        $this->assertContainerBuilderHasService('wj_eloquent.database_manager', '%wj_eloquent.database_manager.class%');
        $this->assertFalse($this->container->has('wj_eloquent.initializer'));
    }

    /** @test */
    public function it_can_enable_eloquent()
    {
        $this->load(array(
            'connections' => $this->getConnectionConfig(),
            'eloquent'    => array('enabled' => true),
        ));

        $this->assertContainerBuilderHasService('wj_eloquent.initializer', '%wj_eloquent.initializer.class%');
    }

    /**
     * @test
     * @expectedException LogicException
     * @expectedExceptionMessage There should be at least one connection configured on "wj_eloquent.connections" in order to use the Eloquent ORM.
     */
    public function it_fails_to_enable_eloquent_without_connections()
    {
        $this->load(array('eloquent' => array('enabled' => true)));
    }

    protected function getConnectionConfig()
    {
        return array(
            'default' => array(
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'database'  => 'db',
                'username'  => 'root',
                'password'  => '',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ),
        );
    }
}
