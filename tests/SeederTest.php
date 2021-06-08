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

use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\MockeryTrait;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/Fixtures/ConsoleCommandFixture.php';

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class SeederTest extends TestCase
{
    use MockeryTrait;

    protected $subject;
    protected $container;

    protected function setUp(): void
    {
        $this->container = \Mockery::mock(ContainerInterface::class);

        $this->subject = new SeederTest_DummySeeder();
        $this->subject->setSfContainer($this->container);
    }

    /** @test */
    public function it_resolves_the_seeder_using_the_container()
    {
        $seeder = \Mockery::mock(Seeder::class);
        $seeder->allows()->setSfContainer()->with($this->container);

        Promise::containerHasService($this->container, 'foo_service', $seeder);

        $this->assertEquals($seeder, $this->subject->resolve('foo_service'));
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

        $this->expectException(\LogicException::class);

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
