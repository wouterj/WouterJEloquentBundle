<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\DependencyInjection\Container;
use WouterJ\EloquentBundle\Migrations\Migrator;
use Prophecy\Argument;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateRollbackCommandTest extends \PHPUnit_Framework_TestCase
{
    private $command;
    /** @var Container */
    private $container;
    private $migrator;

    protected function setUp()
    {
        $this->migrator = $this->prophesize(Migrator::class);
        $this->migrator->getNotes()->willReturn([]);
        $this->migrator->setConnection(Argument::any())->willReturn();

        $this->container = new Container();
        $this->container->setParameter('kernel.environment', 'dev');
        $this->container->set('wouterj_eloquent.migrator', $this->migrator->reveal());

        $this->command = new MigrateRollbackCommand();
        $this->command->setContainer($this->container);
    }

    /** @test */
    public function it_asks_for_confirmation_in_prod()
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->migrator->rollback(Argument::cetera())->shouldNotBeCalled();

        TestCommand::create($this->command)
            ->answering("no")
            ->duringExecute()
            ->outputs('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_does_not_ask_for_confirmation_in_dev()
    {
        $this->migrator->rollback(Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->execute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_always_continues_when_force_is_passed()
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->migrator->rollback(Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->passing('--force')
            ->duringExecute()
            ->doesNotOutput('Are you sure you want to execute the migrations in production?');
    }

    /** @test */
    public function it_allows_changing_the_connection()
    {
        $this->migrator->setConnection('something')->shouldBeCalled();

        $this->migrator->rollback(false)->shouldBeCalled();

        TestCommand::create($this->command)->passing('--database', 'something')->duringExecute();
    }

    /** @test */
    public function it_can_pretend_migrations_were_rolled_back()
    {
        $this->migrator->rollback(true)->shouldBeCalled();

        TestCommand::create($this->command)->passing('--pretend')->duringExecute();
    }

    /** @test */
    public function it_outputs_migration_notes()
    {
        $this->migrator->getNotes()->willReturn([
            'Rolled back: CreateFlightsTable',
            'Rolled back: SomethingToTest',
        ]);

        $this->migrator->rollback(Argument::cetera())->shouldBeCalled();

        TestCommand::create($this->command)
            ->execute()
            ->outputs("Rolled back: CreateFlightsTable\nRolled back: SomethingToTest");
    }
}
