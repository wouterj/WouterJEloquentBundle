<?php

namespace Wj\EloquentBundle\Tests\Facade;

use Wj\EloquentBundle\Facade\AliasesLoader;

class AliasesLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $subject;

    public function setUp()
    {
        $this->subject = new AliasesLoader();
        $this->subject->register();
    }

    /** @test */
    public function it_aliases_the_correct_classes()
    {
        $this->subject->addAlias('AD', __NAMESPACE__.'\AliasDummy');

        $this->assertInstanceOf(__NAMESPACE__.'\AliasDummy', new \AD);
    }

    /** @test */
    public function it_works_in_every_namespace()
    {
        $class = __NAMESPACE__.'\AliasDummy1';
        $this->subject->addAlias('AD1', $class);

        $this->assertInstanceOf($class, new AD1);
        $this->assertInstanceOf($class, new \Foo\Bar\AD1);
        $this->assertInstanceOf($class, new Foo\Bar\AD1);
    }
}

class AliasDummy { }
class AliasDummy1 { }
