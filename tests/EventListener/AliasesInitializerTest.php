<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class FacadeInitializerTest extends TestCase
{
    protected $loader;
    protected $container;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->loader = $this->prophesize(AliasesLoader::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->subject = new FacadeInitializer($this->container->reveal());
    }

    /** @test */
    public function it_configures_the_facade()
    {
        $this->subject->initialize();

        $container = $this->readAttribute(Facade::class, 'container');
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
