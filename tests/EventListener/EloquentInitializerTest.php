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
use PHPUnit\Framework\TestCase;
use WouterJ\EloquentBundle\MockeryTrait;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class EloquentInitializerTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    protected $capsule;
    protected $subject;

    protected function doSetUp()
    {
        $this->capsule = \Mockery::mock(Manager::class);
        $this->subject = new EloquentInitializer($this->capsule);
    }

    /** @test */
    public function it_registers_the_loader()
    {
        $this->capsule->shouldReceive('bootEloquent')->once();

        $this->subject->initialize();
    }
}
