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
use WouterJ\EloquentBundle\Migrations\Migrator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateCommandTest extends TestCase
{
    use SetUpTearDownTrait;

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
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'prod');

        $this->migrator->run(Argument::cetera())->shouldNotBeCalled();

        TestCommand::create($command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run(Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'prod');

        $this->migrator->run(Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_uses_the_default_migration_path()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run([__DIR__.'/migrations'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)->execute();
    }

    /** @test */
    public function it_allows_to_specify_another_path()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run([getcwd().'/db'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)->passing('--path', 'db')->duringExecute();
    }

    /** @test */
    public function it_allows_multiple_migration_directories()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->paths()->willReturn(['/somewhere/migrations']);

        $this->migrator->run([__DIR__.'/migrations', '/somewhere/migrations'], Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)->execute();
    }

    /** @test */
    public function it_allows_batching_migrations_one_by_one()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run(Argument::any(), ['pretend' => false, 'step' => true])->shouldBeCalled();

        TestCommand::create($command)->passing('--step')->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_run()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run(Argument::any(), ['pretend' => true, 'step' => false])->shouldBeCalled();

        TestCommand::create($command)->passing('--pretend')->duringExecute();
    }

    /** @test */
    public function it_seeds_after_migrations_when_seed_is_passed()
    {
        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->run(Argument::cetera())->shouldBeCalled();

        $seedCommand = $this->prophesize(Command::class);
        $seedCommand->run(Argument::type(ArrayInput::class), Argument::any())->shouldBeCalled();

        $app = $this->prophesize(Application::class);
        $app->getHelperSet()->willReturn(new HelperSet());
        $app->getDefinition()->willReturn(new InputDefinition());
        $app->find('eloquent:seed')->willReturn($seedCommand->reveal());

        $command->setApplication($app->reveal());

        TestCommand::create($command)->passing('--seed')->duringExecute();
    }

    /** @test */
    public function it_outputs_migration_notes()
    {
        if (!method_exists(Migrator::class, 'getNotes')) {
            $this->markTestSkipped('Only applies to Illuminate <5.7');
        }

        $command = new MigrateCommand($this->migrator->reveal(), __DIR__.'/migrations', 'dev');

        $this->migrator->getNotes()->willReturn([
            'Migrated: CreateFlightsTable',
            'Migrated: SomethingToTest',
        ]);

        $this->migrator->run(Argument::cetera())->shouldBeCalled();

        TestCommand::create($command)
            ->execute()
            ->outputs("Migrated: CreateFlightsTable\nMigrated: SomethingToTest");
    }
}
