<?php

namespace Wj\EloquentBundle\Tests\EventListener;

use Wj\EloquentBundle\Tests\ProphecyTestCase;
use Wj\EloquentBundle\EventListener\AliasesInitializer;

class AliasesInitializerTest extends ProphecyTestCase
{
    protected $loader;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->loader = $this->prophet->prophesize('Wj\EloquentBundle\Facade\AliasesLoader');
        $this->subject = new AliasesInitializer($this->loader->reveal());
    }

    /** @test */
    public function it_registers_the_loader()
    {
        $this->loader->register()->shouldBeCalled();

        $this->subject->initialize();
    }
}
