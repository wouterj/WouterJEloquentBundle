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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Migrator;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateCommandTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    private $migrator;

    protected function doSetUp()
    {
        $this->migrator = \Mockery::mock(Migrator::class);
        $this->migrator->allows()->paths()->andReturn([])->byDefault();
        $this->migrator->allows()->setOutput()->withAnyArgs();

        if (method_exists(Migrator::class, 'getNotes')) {
            $this->migrator->allows()->getNotes()->andReturn([]);
        }
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldNotReceive('run');

        TestCommand::create($command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->withAnyArgs();

        TestCommand::create($command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'prod');

        $this->migrator->shouldReceive('run')->once()->withAnyArgs();

        TestCommand::create($command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->with([__DIR__.'/migrations'], \Mockery::any());

        TestCommand::create($command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->with([getcwd().'/db'], \Mockery::any());

        TestCommand::create($command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->allows()->paths()->andReturn(['/somewhere/migrations']);

        $this->migrator->shouldReceive('run')->once()->with([__DIR__.'/migrations', '/somewhere/migrations'], \Mockery::any());

        TestCommand::create($command)->execute();
    }

    /** @test */
    public function it_allows_batching_migrations_one_by_one()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->with(\Mockery::any(), ['pretend' => false, 'step' => true]);

        TestCommand::create($command)->passing('--step')->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_run()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->with(\Mockery::any(), ['pretend' => true, 'step' => false]);

        TestCommand::create($command)->passing('--pretend')->duringExecute();
    }

    /** @test */
    public function it_seeds_after_migrations_when_seed_is_passed()
    {
        $command = new MigrateCommand($this->migrator, __DIR__.'/migrations', 'dev');

        $this->migrator->shouldReceive('run')->once()->withAnyArgs();

        $seedCommand = \Mockery::mock(new Command('eloquent:seed'));
        $seedCommand->shouldReceive('run')->once()->with(\Mockery::type(ArrayInput::class), \Mockery::any());

        $app = new Application();
        $app->add($seedCommand);

        $command->setApplication($app);

        TestCommand::create($command)->passing('--seed')->duringExecute();
    }
}
