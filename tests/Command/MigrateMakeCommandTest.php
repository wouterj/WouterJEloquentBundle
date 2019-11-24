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

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
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
    use SetUpTearDownTrait;

    private $command;
    private $creator;
    private $migrator;

    protected function doSetUp()
    {
        $this->creator = $this->prophesize(Creator::class);
        $this->migrator = $this->prophesize(Migrator::class);

        $this->migrator->paths()->willReturn([]);

        $this->command = new MigrateMakeCommand($this->creator->reveal(), $this->migrator->reveal(), __DIR__.'/migrations', 'dev');
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
