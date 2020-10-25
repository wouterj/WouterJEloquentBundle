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
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Creator;
use WouterJ\EloquentBundle\Migrations\Migrator;
use WouterJ\EloquentBundle\Promise;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateMakeCommandTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    private $command;
    private $creator;
    private $migrator;

    protected function doSetUp()
    {
        $this->creator = \Mockery::mock(Creator::class);
        $this->migrator = \Mockery::mock(Migrator::class);
        $this->migrator->allows()->paths()->andReturn([])->byDefault();

        $this->command = new MigrateMakeCommand($this->creator, $this->migrator, __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_defaults_to_the_main_migrations_dir()
    {
        $this->migrator->allows()->paths()->andReturn(['/somewhere/migrations']);

        $this->creator->shouldReceive('create')->once()
            ->with(\Mockery::any(), __DIR__.'/migrations', \Mockery::any(), \Mockery::any());

        TestCommand::create($this->command)
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_allows_to_override_the_target_dir()
    {
        $this->creator->shouldReceive('create')->once()
            ->with(\Mockery::any(), getcwd().'/custom/migrations', \Mockery::any(), \Mockery::any());

        TestCommand::create($this->command)
            ->passing('--path', 'custom/migrations')
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_stub_for_table_creation()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('CreateFlightsTable', __DIR__.'/migrations', 'flights', true);

        TestCommand::create($this->command)
            ->passing('--create', 'flights')
            ->passing('name', 'CreateFlightsTable')
            ->duringExecute();
    }

    /** @test */
    public function it_guesses_table_creation_from_migration_name()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('create_flights_table', __DIR__.'/migrations', 'flights', true);

        TestCommand::create($this->command)
            ->passing('name', 'create_flights_table')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_stub_for_updates()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('RenamingNameField', __DIR__.'/migrations', 'flights', false);

        TestCommand::create($this->command)
            ->passing('--table', 'flights')
            ->passing('name', 'RenamingNameField')
            ->duringExecute();
    }

    /** @test */
    public function it_creates_a_blank_stub_when_no_option_was_provided()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('AddDefaultFlights', __DIR__.'/migrations', null, false);

        TestCommand::create($this->command)
            ->passing('name', 'AddDefaultFlights')
            ->duringExecute();
    }
}
