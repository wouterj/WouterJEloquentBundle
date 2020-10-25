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

use Illuminate\Console\OutputStyle;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\DependencyInjection\Container;
use WouterJ\EloquentBundle\Promise;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Migrator;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateResetCommandTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    private $command;
    private $migrator;

    protected function doSetUp()
    {
        $this->migrator = \Mockery::mock(Migrator::class);
        $this->migrator->allows()->paths()->andReturn([])->byDefault();
        $this->migrator->allows()->repositoryExists()->andReturn(true)->byDefault();
        $this->migrator->allows()->setConnection()->withAnyArgs()->byDefault();
        if (method_exists(Migrator::class, 'getNotes')) {
            $this->migrator->allows()->getNotes()->andReturn([]);
        } else {
            $this->migrator->allows()->setOutput()->withAnyArgs();
        }

        $this->command = new MigrateResetCommand($this->migrator, __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $command = new MigrateResetCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldNotReceive('reset')->withAnyArgs();

        TestCommand::create($command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $this->migrator->shouldReceive('reset')->once()->withAnyArgs();

        TestCommand::create($this->command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $command = new MigrateResetCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldReceive('reset')->once()->withAnyArgs();

        TestCommand::create($command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $this->migrator->shouldReceive('reset')->once()->with([__DIR__.'/migrations'], \Mockery::any());

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $this->migrator->shouldReceive('reset')->once()->with([getcwd().'/db'], \Mockery::any());

        TestCommand::create($this->command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $this->migrator->allows()->paths()->andReturn(['/somewhere/migrations']);

        $this->migrator->shouldReceive('reset')->once()
            ->with([__DIR__.'/migrations', '/somewhere/migrations'], \Mockery::any());

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_changing_the_connection()
    {
        $this->migrator->shouldReceive('setConnection')->once()->with('something');

        $this->migrator->shouldReceive('reset')->once()->with(\Mockery::any(), false);

        TestCommand::create($this->command)->passing('--database', 'something')->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_resetted()
    {
        $this->migrator->shouldReceive('reset')->once()->with(\Mockery::any(), true);

        TestCommand::create($this->command)->passing('--pretend')->duringExecute();
    }

    /** @test */
    public function it_stops_when_repository_does_not_exists()
    {
        $this->migrator->allows()->repositoryExists()->andReturn(false);

        $this->migrator->shouldNotReceive('reset');

        TestCommand::create($this->command)
            ->execute()
            ->outputs('Migration table not found.')
            ->exitsWith(1);
    }
}
