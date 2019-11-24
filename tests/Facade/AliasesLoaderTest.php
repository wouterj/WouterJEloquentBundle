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

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class AliasesLoaderTest extends TestCase
{
    use SetUpTearDownTrait;

    protected $subject;

    public function doSetUp()
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
