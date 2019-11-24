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
use WouterJ\EloquentBundle\Migrations\Migrator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateResetCommandTest extends TestCase
{
    use SetUpTearDownTrait;

    private $command;
    private $migrator;

    protected function doSetUp()
    {
        $this->migrator = $this->prophesize(Migrator::class);
        if (method_exists(Migrator::class, 'getNotes')) {
            $this->migrator->getNotes()->willReturn([]);
        } else {
            $this->migrator->setOutput(Argument::type(OutputStyle::class))->willReturn();
        }

        $this->migrator->paths()->willReturn([]);
        $this->migrator->setConnection(Argument::any())->willReturn();
        $this->migrator->repositoryExists()->willReturn(true);

        $this->command = new MigrateResetCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $command = new MigrateResetCommand($this->migrator->reveal(), __DIR__.'/migrations', 'prod');

        $this->migrator->reset(Argument::cetera())->shouldNotBeCalled();

        TestCommand::create($command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $this->migrator->reset(Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $command = new MigrateResetCommand($this->migrator->reveal(), __DIR__.'/migrations', 'prod');

        $this->migrator->reset(Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $this->migrator->reset([__DIR__.'/migrations'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $this->migrator->reset([getcwd().'/db'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $this->migrator->paths()->willReturn(['/somewhere/migrations']);

        $this->migrator->reset([__DIR__.'/migrations', '/somewhere/migrations'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)->execute();
    }

    /** @test */
    public function it_allows_changing_the_connection()
    {
        $this->migrator->setConnection('something')->shouldBeCalled();

        $this->migrator->reset(Argument::any(), false)->shouldBeCalled();

        TestCommand::create($this->command)->passing('--database', 'something')->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_resetted()
    {
        $this->migrator->reset(Argument::any(), true)->shouldBeCalled();

        TestCommand::create($this->command)->passing('--pretend')->duringExecute();
    }

    /** @test */
    public function it_outputs_migration_notes()
    {
        if (!method_exists(Migrator::class, 'getNotes')) {
            $this->markTestSkipped('Only applies to Illuminate <5.7');
        }

        $this->migrator->getNotes()->willReturn([
            'Rolled back: CreateFlightsTable',
            'Rolled back: SomethingToTest',
        ]);

        $this->migrator->reset(Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->execute()
            ->outputs("Rolled back: CreateFlightsTable\nRolled back: SomethingToTest");
    }

    /** @test */
    public function it_stops_when_repository_does_not_exists()
    {
        $this->migrator->repositoryExists()->willReturn(false);

        $this->migrator->reset(Argument::cetera())->shouldNotBeCalled();

        TestCommand::create($this->command)
            ->execute()
            ->outputs('Migration table not found.')
            ->exitsWith(1);
    }
}
