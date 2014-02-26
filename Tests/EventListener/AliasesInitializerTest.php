<?php

namespace Wj\EloquentBundle\Tests\EventListener;

use Wj\EloquentBundle\Tests\ProphecyTestCase;
use Wj\EloquentBundle\Facade\Facade;
use Wj\EloquentBundle\EventListener\FacadeInitializer;

class FacadeInitializerTest extends ProphecyTestCase
{
    protected $loader;
    protected $container;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->loader = $this->prophet->prophesize('Wj\EloquentBundle\Facade\AliasesLoader');
        $this->container = $this->prophet->prophesize('Symfony\Component\DependencyInjection\Container');
        $this->subject = new FacadeInitializer($this->container->reveal());
    }

    /** @test */
    public function it_configures_the_facade()
    {
        $this->subject->initialize();

        $container = $this->readAttribute('Wj\EloquentBundle\Facade\Facade', 'container');
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
