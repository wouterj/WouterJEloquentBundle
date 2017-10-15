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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Migrations\Creator;
use WouterJ\EloquentBundle\Migrations\Migrator;
use WouterJ\EloquentBundle\Promise;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateMakeCommandTest extends TestCase
{
    private $command;
    private $container;
    private $creator;

    protected function setUp()
    {
        $this->creator = $this->prophesize(Creator::class);
        $this->migrator = $this->prophesize(Migrator::class);
        $this->container = $this->prophesize(ContainerInterface::class);

        $this->migrator->paths()->willReturn([]);

        Promise::containerHasService($this->container, 'wouterj_eloquent.migrations.creator', $this->creator->reveal());
        Promise::containerHasService($this->container, 'wouterj_eloquent.migrator', $this->migrator->reveal());
        Promise::containerHasParameter($this->container, 'wouterj_eloquent.migration_path', __DIR__.'/migrations');

        $this->command = new MigrateMakeCommand();
        $this->command->setcontainer($this->container->reveal());
    }

    /** @test */
    public function it_defaults_to_the_main_migrations_dir()
    {
        $this->migrator->paths()->willReturn(['/somewhere/migrations']);

        $this->creator->create(Argument::any(), __DIR__.'/migrations', Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_allows_to_override_the_target_dir()
    {
        $this->creator->create(Argument::any(), getcwd().'/custom/migrations', Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('--path', 'custom/migrations')
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_stub_for_table_creation()
    {
        $this->creator->create('CreateFlightsTable', __DIR__.'/migrations', 'flights', true)->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('--create', 'flights')
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_guesses_table_creation_from_migration_name()
    {
        $this->creator->create('create_flights_table', __DIR__.'/migrations', 'flights', true)->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('name', 'create_flights_table')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_stub_for_updates()
    {
        $this->creator->create('RenamingNameField', __DIR__.'/migrations', 'flights', false)->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('--table', 'flights')
            ->passing('name', 'RenamingNameField')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_blank_stub_when_no_option_was_provided()
    {
        $this->creator->create('AddDefaultFlights', __DIR__.'/migrations', null, false)->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('name', 'AddDefaultFlights')
            ->duringExecute();
    }
}
