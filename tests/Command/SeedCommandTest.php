<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Command;

use Illuminate\Database\DatabaseManager;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Promise;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeedCommandTest extends TestCase
{
    use MockeryTrait;

    protected $container;
    protected $command;
    protected $manager;

    public function setUp(): void
    {
        $this->container = \Mockery::mock(ContainerInterface::class);
        $this->manager = \Mockery::mock(DatabaseManager::class);
        $this->manager->allows()->setDefaultConnection()->withAnyArgs();

        $this->command = new SeedCommand($this->container, $this->manager, [], 'dev');
    }

    #[Test]
    public function it_executes_specified_classes()
    {
        $seederClass = __CLASS__.'_DummySeeder';
        $seeder1Class = __CLASS__.'_SecondDummySeeder';

        Promise::containerDoesNotHaveService($this->container, $seederClass);
        Promise::containerDoesNotHaveService($this->container, $seeder1Class);

        $test = TestCommand::create($this->command)
            ->passing('--database')
            ->passing('class', [$seederClass, $seeder1Class])
            ->duringExecute()
        ;
        $test->outputsRegex('/'.preg_quote($seederClass).'[\s\.]* [\d\.]+ms DONE\s+'.preg_quote($seeder1Class).'[\s\.]* [\d\.]+ms DONE/');
    }
}

class SeedCommandTest_DummySeeder extends Seeder { public function run() { } }
class SeedCommandTest_SecondDummySeeder extends Seeder { public function run() { } }
