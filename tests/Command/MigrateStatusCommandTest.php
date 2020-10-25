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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Migrator;
use WouterJ\EloquentBundle\Promise;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateStatusCommandTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    private $command;
    private $repository;
    private $migrator;

    protected function doSetUp()
    {
        $this->repository = \Mockery::mock(MigrationRepositoryInterface::class);
        $this->repository->allows()->getRan()->andReturn([])->byDefault();

        $this->migrator = \Mockery::mock(Migrator::class);
        $this->migrator->allows()->paths()->andReturn([])->byDefault();
        $this->migrator->allows()->repositoryExists()->andReturn(true);
        $this->migrator->allows()->getRepository()->andReturn($this->repository);
        $this->migrator->allows()->setConnection()->withAnyArgs();
        $this->migrator->allows()->getMigrationName()->withAnyArgs()->andReturnArg(0);
        $this->migrator->allows()->getMigrationFiles()->withAnyArgs()->andReturn(['Migration1', 'Migration2'])->byDefault();

        $this->command = new MigrateStatusCommand($this->migrator, __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_outputs_migration_statuses()
    {
        $this->repository->allows()->getRan()->andReturn(['Migration1']);

        TestCommand::create($this->command)
            ->execute()
            ->outputs(" ====== ============ \n  Ran?   Migration   \n ====== ============ \n  Y      Migration1  \n  N      Migration2  \n ====== ============");
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $this->migrator->shouldReceive('getMigrationFiles')
            ->atLeast()->once()
            ->with([__DIR__.'/migrations'])
            ->andReturn([]);

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $this->migrator->shouldReceive('getMigrationFiles')
            ->atLeast()->once()
            ->with([getcwd().'/db'])
            ->andReturn([]);

        TestCommand::create($this->command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $this->migrator->allows()->paths()->andReturn(['/somewhere/migrations']);

        $this->migrator->shouldReceive('getMigrationFiles')
            ->atLeast()->once()
            ->with([__DIR__.'/migrations', '/somewhere/migrations'])
            ->andReturn([]);

        TestCommand::create($this->command)->execute();
    }
}
