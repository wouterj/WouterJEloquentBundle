<?php

namespace WouterJ\EloquentBundle\Tests\DependencyInjection;

use WouterJ\EloquentBundle\DependencyInjection\WouterJEloquentExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Illuminate\Database\Eloquent\Model as Eloquent;

class WouterJEloquentExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions()
    {
        return array(new WouterJEloquentExtension());
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

        $this->assertContainerBuilderHasService('wouterj_eloquent', '%wouterj_eloquent.class%');
        $this->assertContainerBuilderHasService('wouterj_eloquent.database_manager', '%wouterj_eloquent.database_manager.class%');
        $this->assertFalse($this->container->has('wouterj_eloquent.initializer'));
    }

    /** @test */
    public function it_can_enable_eloquent()
    {
        $this->load(array(
            'connections' => $this->getConnectionConfig(),
            'eloquent'    => array('enabled' => true),
        ));

        $this->assertContainerBuilderHasService('wouterj_eloquent.initializer', '%wouterj_eloquent.initializer.class%');
    }

    /**
     * @test
     * @expectedException LogicException
     * @expectedExceptionMessage There should be at least one connection configured on "wouterj_eloquent.connections" in order to use the Eloquent ORM.
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
