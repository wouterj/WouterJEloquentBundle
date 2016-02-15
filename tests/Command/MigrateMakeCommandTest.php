<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Migrations\Creator;
use WouterJ\EloquentBundle\Promise;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateMakeCommandTest extends \PHPUnit_Framework_TestCase
{
    private $subject;
    private $container;
    private $creator;
    private $input;
    private $output;

    protected function setUp()
    {
        $this->creator = $this->prophesize(Creator::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(OutputInterface::class);

        Promise::containerHasService($this->container, 'wouterj_eloquent.migrations.creator', $this->creator->reveal());
        Promise::containerHasParameter($this->container, 'kernel.root_dir', __DIR__);

        $this->subject = new MigrateMakeCommand();
        $this->subject->setcontainer($this->container->reveal());
    }

    /** @test */
    public function it_creates_a_stub_for_table_creation()
    {
        Promise::inputHasArgument($this->input, 'name', 'CreateFlightsTable');
        Promise::inputHasOption($this->input, 'create', 'flights');
        Promise::inputHasOption($this->input, 'table', null);

        $this->creator->create('CreateFlightsTable', __DIR__.'/migrations', 'flights', true)->shouldBeCalled();

        $this->subject->execute($this->input->reveal(), $this->output->reveal());
    }

    /** @test */
    public function it_creates_a_stub_for_updates()
    {
        Promise::inputHasArgument($this->input, 'name', 'RenamingNameField');
        Promise::inputHasOption($this->input, 'table', 'flights');
        Promise::inputHasOption($this->input, 'create', null);

        $this->creator->create('RenamingNameField', __DIR__.'/migrations', 'flights', false)->shouldBeCalled();

        $this->subject->execute($this->input->reveal(), $this->output->reveal());
    }

    /** @test */
    public function it_creates_a_blank_stub_when_no_option_was_provided()
    {
        Promise::inputHasArgument($this->input, 'name', 'AddDefaultFlights');
        Promise::inputHasOption($this->input, 'table', null);
        Promise::inputHasOption($this->input, 'create', null);

        $this->creator->create('AddDefaultFlights', __DIR__.'/migrations', null, false)->shouldBeCalled();

        $this->subject->execute($this->input->reveal(), $this->output->reveal());
    }
}
