<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Tests;

use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Tests\Promise;

require_once __DIR__.'/Fixtures/ConsoleCommandFixture.php';

class SeederTest extends ProphecyTestCase
{
    protected $subject;
    protected $container;
    protected $seeder;

    public function setUp()
    {
        parent::setUp();

        $this->container = $this->prophet->prophesize('Symfony\Component\DependencyInjection\Container');
        $this->seeder = $this->prophet->prophesize('WouterJ\EloquentBundle\Seeder');

        $this->subject = new Seeder();
        $this->subject->setSfContainer($this->container->reveal());
    }

    /** @test */
    public function it_resolves_the_seeder_using_the_container()
    {
        Promise::containerHasService($this->container, 'foo_service', $this->seeder->reveal());

        $this->subject->resolve('foo_service');
    }

    /** @test */
    public function it_resolves_instantiate_the_seeder_without_container()
    {
        $class = __NAMESPACE__.'\DummySeeder';
        Promise::containerDoesNotHaveService($this->container, $class);

        $this->assertInstanceOf($class, $this->subject->resolve($class));
    }

    /** @test */
    public function it_fails_if_seeder_does_not_extend_base_seeder()
    {
        Promise::containerHasService($this->container, 'foo_service', new WrongSeeder);

        $this->setExpectedException('LogicException');

        $this->subject->resolve('foo_service');
    }

    public function it_fails_if_seeder_extends_illuminate_seeder()
    {
        Promise::containerHasService($this->container, 'foo_service', new LaravelSeeder);

        $this->setExpectedException('LogicException');

        $this->subject->resolve('foo_service');
    }
}

class DummySeeder extends Seeder { }
class WrongSeeder { }
class LaravelSeeder extends \Illuminate\Database\Seeder { }
