<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Facade;

use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Fixtures\Facade as Fixture;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class FacadeTest extends TestCase
{
    /** @test */
    public function it_accepts_object_accessors()
    {
        $this->assertEquals(Fixture\Dummy::class, Fixture\ObjectFacade::foo());
    }

    /** @test */
    public function it_accepts_container_accessors()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('facade_service')->willReturn(true);
        $container->get('facade_service')->shouldBeCalled()->willReturn(new Dummy);

        Facade::setContainer($container->reveal());

        $this->assertEquals(Dummy::class, DummyFacade::foo());
    }
}

class Dummy
{
    public function foo()
    {
        return __CLASS__;
    }
}

class DummyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'facade_service';
    }
}
