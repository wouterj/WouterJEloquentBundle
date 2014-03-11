<?php

namespace WouterJ\EloquentBundle\Tests\Command;

use WouterJ\EloquentBundle\Seeder;
use WouterJ\EloquentBundle\Tests\Promise;
use WouterJ\EloquentBundle\Tests\Prediction;
use WouterJ\EloquentBundle\Tests\ProphecyTestCase;
use WouterJ\EloquentBundle\Command\SeedCommand;

class SeedCommandTest extends ProphecyTestCase
{
    protected $container;
    protected $input;
    protected $output;
    protected $subject;

    public function setUp()
    {
        parent::setUp();

        $this->container = $this->prophet->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->input = $this->prophet->prophesize('Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->prophet->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $this->subject = new SeedCommand();
        $this->subject->setContainer($this->container->reveal());
    }

    /** @test */
    public function it_executes_specified_classes()
    {
        $seederClass = __NAMESPACE__.'\DummySeeder';
        $seeder1Class = __NAMESPACE__.'\SecondDummySeeder';

        Promise::inputHasArgument($this->input, 'class', array($seederClass, $seeder1Class));
        Promise::inputHasOption($this->input, 'database', null);

        $manager = $this->prophet->prophesize('Illuminate\Database\DatabaseManager');
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

class DummySeeder extends Seeder { }
class SecondDummySeeder extends Seeder { }
