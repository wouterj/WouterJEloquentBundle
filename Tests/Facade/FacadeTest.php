<?php

namespace Wj\EloquentBundle\Tests\Facade;

use Wj\EloquentBundle\Tests\ProphecyTestCase;
use Wj\EloquentBundle\Facade\Facade;
use Wj\EloquentBundle\Tests\Fixtures\Facade as Fixture;

class FacadeTest extends ProphecyTestCase
{
    /** @test */
    public function it_accepts_object_accessors()
    {
        Fixture\ObjectFacade::foo();
    }

    /** @test */
    public function it_accepts_container_accessors()
    {
        $container = $this->prophet->prophesize('Symfony\Component\DependencyInjection\Container');
        $container->has('facade_service')->willReturn(true);
        $container->get('facade_service')->shouldBeCalled()->willReturn(new Dummy);

        Facade::setContainer($container->reveal());

        Fixture\ContainerFacade::foo();
    }
}

class Dummy
{
    public function foo()
    { }
}
