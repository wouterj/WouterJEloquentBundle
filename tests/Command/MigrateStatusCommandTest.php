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
use WouterJ\EloquentBundle\Migrations\Migrator;
use WouterJ\EloquentBundle\Promise;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateStatusCommandTest extends TestCase
{
    use SetUpTearDownTrait;

    private $command;
    private $repository;
    private $migrator;

    protected function doSetUp()
    {
        $this->repository = $this->prophesize(MigrationRepositoryInterface::class);
        $this->repository->getRan()->willReturn([]);

        $this->migrator = $this->prophesize(Migrator::class);
        $this->migrator->paths()->willReturn([]);
        $this->migrator->setConnection(Argument::any())->willReturn(null);
        $this->migrator->repositoryExists()->willReturn(true);
        $this->migrator->getRepository()->willReturn($this->repository->reveal());
        $this->migrator->getMigrationName(Argument::any())->willReturnArgument(0);
        $this->migrator->getMigrationFiles(Argument::any())->willReturn(['Migration1', 'Migration2']);

        $this->command = new MigrateStatusCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_outputs_migration_statuses()
    {
        $this->repository->getRan()->willReturn(['Migration1']);

        TestCommand::create($this->command)
            ->execute()
            ->outputs(" ====== ============ \n  Ran?   Migration   \n ====== ============ \n  Y      Migration1  \n  N      Migration2  \n ====== ============");
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $this->migrator->getMigrationFiles([__DIR__.'/migrations'])->shouldBeCalled()->willReturn([]);

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $this->migrator->getMigrationFiles([getcwd().'/db'])->shouldBeCalled()->willReturn([]);

        TestCommand::create($this->command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $this->migrator->paths()->willReturn(['/somewhere/migrations']);

        $this->migrator->getMigrationFiles([__DIR__.'/migrations', '/somewhere/migrations'])->shouldBeCalled()->willReturn([]);

        TestCommand::create($this->command)->execute();
    }
}
