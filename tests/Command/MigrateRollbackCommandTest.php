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
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Migrator;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateRollbackCommandTest extends TestCase
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
        $this->migrator->allows()->setConnection()->withAnyArgs()->byDefault();
        if (method_exists(Migrator::class, 'getNotes')) {
            $this->migrator->allows()->getNotes()->andReturn([]);
        } else {
            $this->migrator->allows()->setOutput()->withAnyArgs();
        }

        $this->command = new MigrateRollbackCommand($this->migrator, __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $command = new MigrateRollbackCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldNotReceive('rollback');

        TestCommand::create($command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $this->migrator->shouldReceive('rollback')->once();

        TestCommand::create($this->command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $command = new MigrateRollbackCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldReceive('rollback')->once();

        TestCommand::create($command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $this->migrator->shouldReceive('rollback')->once()->with([__DIR__.'/migrations'], \Mockery::any());

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $this->migrator->shouldReceive('rollback')->once()->with([getcwd().'/db'], \Mockery::any());

        TestCommand::create($this->command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $this->migrator->allows()->paths()->andReturn(['/somewhere/migrations']);

        $this->migrator->shouldReceive('rollback')->once()
            ->with([__DIR__.'/migrations', '/somewhere/migrations'], \Mockery::any());

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_changing_the_connection()
    {
        $this->migrator->shouldReceive('setConnection')->once()->with('something');

        $this->migrator->shouldReceive('rollback')->once()
            ->with(\Mockery::any(), ['pretend' => false, 'step' => 0]);

        TestCommand::create($this->command)->passing('--database', 'something')->duringExecute();
    }

    /** @test */
    public function it_allows_to_revert_multiple_migrations()
    {
        $this->migrator->shouldReceive('rollback')->once()
            ->with(\Mockery::any(), ['pretend' => false, 'step' => 4]);

        TestCommand::create($this->command)->passing('--step', 4)->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_rolled_back()
    {
        $this->migrator->shouldReceive('rollback')->once()
            ->with(\Mockery::any(), ['pretend' => true, 'step' => 0]);

        TestCommand::create($this->command)->passing('--pretend')->duringExecute();
    }
}
