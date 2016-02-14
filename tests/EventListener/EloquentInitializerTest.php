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

use Illuminate\Database\Capsule\Manager;

class EloquentInitializerTest extends \PHPUnit_Framework_TestCase
{
    protected $capsule;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->capsule = $this->prophesize(Manager::class);
        $this->subject = new EloquentInitializer($this->capsule->reveal());
    }

    /** @test */
    public function it_registers_the_loader()
    {
        $this->capsule->bootEloquent()->shouldBeCalled();

        $this->subject->initialize();
    }
}
