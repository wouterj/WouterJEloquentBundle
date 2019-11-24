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
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Promise;
use WouterJ\EloquentBundle\Prediction;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class SeedCommandTest extends TestCase
{
    use SetUpTearDownTrait;

    protected $container;
    protected $command;
    protected $manager;

    public function doSetUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->manager = $this->prophesize(DatabaseManager::class);

        $this->command = new SeedCommand($this->container->reveal(), $this->manager->reveal(), [], 'dev');
    }

    /** @test */
    public function it_executes_specified_classes()
    {
        $seederClass = __CLASS__.'_DummySeeder';
        $seeder1Class = __CLASS__.'_SecondDummySeeder';

        Promise::containerDoesNotHaveService($this->container, $seederClass);
        Promise::containerDoesNotHaveService($this->container, $seeder1Class);

        TestCommand::create($this->command)
            ->passing('--database')
            ->passing('class', [$seederClass, $seeder1Class])
            ->duringExecute()
            ->outputs(<<<EOT
Seeded: $seederClass
Seeded: $seeder1Class
EOT
);
    }

    public function it_checks_all_bundles()
    {
    }
}

class SeedCommandTest_DummySeeder extends Seeder { public function run() { } }
class SeedCommandTest_SecondDummySeeder extends Seeder { public function run() { } }
