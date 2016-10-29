<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2016 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Migrations;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $subject;
    protected $migrationsPath;

    protected function setUp()
    {
        $this->subject = new Creator();
        $this->migrationsPath = sys_get_temp_dir();
    }

    /**
     * @test
     * @dataProvider getMigrationTypes
     */
    public function it_bootstraps_blank_migrations($type, $table = null, $create = false)
    {
        $path = $this->subject->create(ucfirst($type).'Migration', $this->migrationsPath, $table, $create);

        $this->assertMigrationEquals($type, $path);
    }

    public function getMigrationTypes()
    {
        return [
            ['blank'],
            ['create', 'SomeTable', true],
            ['update', 'SomeTable'],
        ];
    }

    private function assertMigrationEquals($name, $actual)
    {
        $this->assertFileExists($actual);

        $normalize = function ($str) { return preg_replace('/\R/', "\n", $str); };

        $expected = $normalize(file_get_contents(__DIR__.'/../Fixtures/migrations/'.$name.'.php'));
        $actual = $normalize(file_get_contents($actual));

        $this->assertEquals($expected, $actual);
    }
}
