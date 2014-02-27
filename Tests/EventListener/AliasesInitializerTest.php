<?php

namespace WouterJ\EloquentBundle\Tests\EventListener;

use WouterJ\EloquentBundle\Tests\ProphecyTestCase;
use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\EventListener\FacadeInitializer;

class FacadeInitializerTest extends ProphecyTestCase
{
    protected $loader;
    protected $container;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->loader = $this->prophet->prophesize('WouterJ\EloquentBundle\Facade\AliasesLoader');
        $this->container = $this->prophet->prophesize('Symfony\Component\DependencyInjection\Container');
        $this->subject = new FacadeInitializer($this->container->reveal());
    }

    /** @test */
    public function it_configures_the_facade()
    {
        $this->subject->initialize();

        $container = $this->readAttribute('WouterJ\EloquentBundle\Facade\Facade', 'container');
        $this->assertSame($this->container->reveal(), $container);
    }

    /** @test */
    public function it_registers_the_loader_when_provided()
    {
        $this->loader->register()->shouldBeCalled();
        $this->subject->setLoader($this->loader->reveal());

        $this->subject->initialize();
    }
}
