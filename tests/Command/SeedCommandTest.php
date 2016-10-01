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

use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Promise;
use WouterJ\EloquentBundle\Prediction;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class SeedCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $input;
    protected $output;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->container = $this->prophesize(ContainerInterface::class);
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->subject = new SeedCommand();
        $this->subject->setContainer($this->container->reveal());
    }

    /** @test */
    public function it_executes_specified_classes()
    {
        $seederClass = __CLASS__.'_DummySeeder';
        $seeder1Class = __CLASS__.'_SecondDummySeeder';

        Promise::inputHasArgument($this->input, 'class', [$seederClass, $seeder1Class]);
        Promise::inputHasOption($this->input, 'database', null);

        $manager = $this->prophesize('Illuminate\Database\DatabaseManager');
        Promise::containerHasService($this->container, 'wouterj_eloquent.database_manager', $manager->reveal());
        Promise::containerDoesNotHaveService($this->container, $seederClass);
        Promise::containerDoesNotHaveService($this->container, $seeder1Class);

        Prediction::outputWritesLine($this->output, '<info>Seeded:</info> '.$seederClass);
        Prediction::outputWritesLine($this->output, '<info>Seeded:</info> '.$seeder1Class);

        $this->subject->execute($this->input->reveal(), $this->output->reveal());
    }

    public function it_checks_all_bundles()
    {
    }
}

class SeedCommandTest_DummySeeder extends Seeder { public function run() { } }
class SeedCommandTest_SecondDummySeeder extends Seeder { public function run() { } }
