<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Promise;
use Symfony\Component\DependencyInjection\ContainerInterface;

require_once __DIR__.'/Fixtures/ConsoleCommandFixture.php';

class SeederTest extends \PHPUnit_Framework_TestCase
{
    protected $subject;
    protected $container;
    protected $seeder;

    public function setUp()
    {
        parent::setUp();

        $this->container = $this->prophesize(ContainerInterface::class);
        $this->seeder = $this->prophesize(Seeder::class);

        $this->subject = new SeederTest_DummySeeder();
        $this->subject->setSfContainer($this->container->reveal());
    }

    /** @test */
    public function it_resolves_the_seeder_using_the_container()
    {
        Promise::containerHasService($this->container, 'foo_service', $this->seeder->reveal());

        $this->subject->resolve('foo_service');
    }

    /** @test */
    public function it_instantiates_the_seeder_without_container()
    {
        $class = __CLASS__.'_DummySeeder';
        Promise::containerDoesNotHaveService($this->container, $class);

        $this->assertInstanceOf($class, $this->subject->resolve($class));
    }

    /** @test */
    public function it_fails_if_seeder_does_not_extend_base_seeder()
    {
        Promise::containerHasService($this->container, 'foo_service', new SeederTest_WrongSeeder());

        $this->setExpectedException('LogicException');

        $this->subject->resolve('foo_service');
    }

    public function it_fails_if_seeder_extends_illuminate_seeder()
    {
        Promise::containerHasService($this->container, 'foo_service', new SeerderTest_LaravelSeeder());

        $this->setExpectedException('LogicException');

        $this->subject->resolve('foo_service');
    }
}

class SeederTest_DummySeeder extends Seeder { public function run() { } }
class SeederTest_WrongSeeder { }
class SeerderTest_LaravelSeeder extends \Illuminate\Database\Seeder { public function run() { } }
