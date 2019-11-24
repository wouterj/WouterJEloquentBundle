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

use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

class CreatorTest extends TestCase
{
    use SetUpTearDownTrait;

    protected $subject;
    protected $migrationsPath;

    protected function doSetUp()
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

    /** @test */
    public function it_generates_the_migration_directory_if_needed()
    {
        $this->subject->create('BlankMigration', $dir = $this->migrationsPath.'/'.uniqid());

        $this->assertDirectoryExists($dir);
    }

    private function assertMigrationEquals($name, $actual)
    {
        $this->assertFileExists($actual);

        $normalize = function ($str) { return preg_replace('/\R/', "\n", $str); };

        switch ($name) {
            case 'create':
                if (method_exists(Blueprint::class, 'integerIncrements')) {
                    $name .= '-5.8';
                }

                $expected = $normalize(file_get_contents(__DIR__.'/../Fixtures/migrations/'.$name.'.php'));

                break;
            default:
                $expected = $normalize(file_get_contents(__DIR__.'/../Fixtures/migrations/'.$name.'.php'));
        }
        $actual = $normalize(file_get_contents($actual));

        $this->assertEquals($expected, $actual);
    }
}
