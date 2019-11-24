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

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class FacadeInitializerTest extends TestCase
{
    use SetUpTearDownTrait;

    protected $loader;
    protected $container;
    protected $subject;

    public function doSetUp()
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

        $refl = new \ReflectionClass(Facade::class);
        $container = $refl->getStaticProperties()['container'];
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
