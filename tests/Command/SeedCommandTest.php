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

use Illuminate\Console\View\Components;
use Illuminate\Database\DatabaseManager;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Promise;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
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

    /** @test */
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
        if (class_exists(Components\Task::class)) {
            $test->outputsRegex('/'.preg_quote($seederClass).' \.+ \d+ms DONE\s+'.preg_quote($seeder1Class).' \.+ \d+ms DONE/');
        } else {
            // BC Laravel <9.39
            $test->outputsRegex('/RUNNING: '.preg_quote($seederClass).'\s+DONE: '.preg_quote($seederClass).' \(\d+ms\)/');
            $test->outputsRegex('/RUNNING: '.preg_quote($seeder1Class).'\s+DONE: '.preg_quote($seeder1Class).' \(\d+ms\)/');
        }
    }
}

class SeedCommandTest_DummySeeder extends Seeder { public function run() { } }
class SeedCommandTest_SecondDummySeeder extends Seeder { public function run() { } }
