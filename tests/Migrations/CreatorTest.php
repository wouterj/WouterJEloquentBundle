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
use Symfony\Bundle\MakerBundle\FileManager;
use WouterJ\EloquentBundle\MockeryTrait;

class CreatorTest extends TestCase
{
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    protected $fileManager;
    protected $subject;
    protected $migrationsPath;

    protected function doSetUp()
    {
        $this->fileManager = \Mockery::mock(FileManager::class);
        $this->subject = new Creator($this->fileManager);
        $this->migrationsPath = sys_get_temp_dir();
    }

    /**
     * @test
     * @dataProvider getMigrationTypes
     */
    public function it_bootstraps_blank_migrations($type, $table = null, $create = false)
    {
        $this->expectMigration($type, $type.'_migration');

        $this->subject->create($type.'_migration', $this->migrationsPath, $table, $create);
    }

    public function getMigrationTypes()
    {
        return [
            ['blank'],
            ['create', 'SomeTable', true],
            ['update', 'SomeTable'],
        ];
    }

    private function expectMigration(string $type, string $name)
    {
        $normalize = function ($str) { return preg_replace('/\R/', "\n", $str); };

        switch ($type) {
            case 'create':
                $eloquent7 = file_exists($this->subject->stubPath().'/migration.stub');
                if (!$eloquent7) {
                    $type .= '-6';
                }

                $expected = $normalize(file_get_contents(__DIR__.'/../Fixtures/migrations/'.$type.'.php'));

                break;
            default:
                $expected = $normalize(file_get_contents(__DIR__.'/../Fixtures/migrations/'.$type.'.php'));
        }

        $this->fileManager->shouldReceive('dumpFile')->once()
            ->with(\Mockery::pattern('/'.preg_quote($this->migrationsPath, '/').'\/\d{4}_\d{2}_\d{2}_\d{6}_'.preg_quote($name, '/').'\.php/'), $expected);
    }
}
