<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Maker;

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use WouterJ\EloquentBundle\MockeryTrait;
use WouterJ\EloquentBundle\Migrations\Creator;
use PHPUnit\Framework\TestCase;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MakeMigrationTest extends TestCase
{
    use MakerTestTrait;
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    protected $defaultOptions = ['command' => 'make:eloquent-migration'];
    private $creator;

    protected function doSetUp()
    {
        $this->creator = \Mockery::mock(Creator::class);
        $this->maker = new MakeMigration($this->creator, __DIR__.'/migrations');
    }

    /** @test */
    public function it_defaults_to_the_main_migrations_dir()
    {
        $this->creator->shouldReceive('create')->once()
            ->with(\Mockery::any(), __DIR__.'/migrations', \Mockery::andAnyOthers());

        $this->callGenerate(['name' => 'CreateFlightsTable']);
    }

    /** @test */
    public function it_creates_a_stub_for_table_creation()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('create_flights_table', __DIR__.'/migrations', 'flights', true);

        $this->callGenerate(['--create' => 'flights', 'name' => 'CreateFlightsTable']);
    }

    /** @test */
    public function it_guesses_table_creation_from_migration_name()
    {
        $this->creator->shouldReceive('create')->twice()
            ->with('create_flights_table', __DIR__.'/migrations', 'flights', true);

        $this->callGenerate(['name' => 'create_flights_table']);
        $this->callGenerate(['name' => 'CreateFlightsTable']);
    }

    /** @test */
    public function it_creates_a_stub_for_updates()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('renaming_name_field', __DIR__.'/migrations', 'flights', false);

        $this->callGenerate(['--table' => 'flights', 'name' => 'RenamingNameField']);
    }

    /** @test */
    public function it_creates_a_blank_stub_when_no_option_was_provided()
    {
        $this->creator->shouldReceive('create')->once()
            ->with('add_default_flights', __DIR__.'/migrations', null, false);

        $this->callGenerate(['name' => 'AddDefaultFlights']);
    }
}
