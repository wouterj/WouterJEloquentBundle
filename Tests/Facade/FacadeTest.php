<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace WouterJ\EloquentBundle\Tests\Facade;

use WouterJ\EloquentBundle\Tests\ProphecyTestCase;
use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\Tests\Fixtures\Facade as Fixture;

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
