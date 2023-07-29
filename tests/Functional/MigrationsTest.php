<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Functional;

use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use PHPUnit\Runner\Version;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use WouterJ\EloquentBundle\Facade\Db;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrationsTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return 'TestKernel';
    }

    protected function setUp(): void
    {
        if (!trait_exists(WithoutModelEvents::class)) {
            // BC with Laravel <9
            $migration = __DIR__.'/app/migrations/2015_02_16_203700_CreateUsersTable.php';
            file_put_contents($migration, str_replace('return new class', 'class CreateUsersTable', file_get_contents($migration)));
        }
    }

    public function testRunningMigrations()
    {
        static::bootKernel();

        $app = new ApplicationTester($a = new Application(static::$kernel));
        $a->setAutoExit(false);

        // reset, in case tests were run previously
        $app->run(['command' => 'eloquent:migrate:reset'], ['decorated' => false]);

        $app->run(['command' => 'eloquent:migrate', '--seed' => true], ['decorated' => false]);

        $regex = '/^\s+2015_02_16_203700_CreateUsersTable \.+ [0-9.]+ms DONE/m';
        if (!class_exists(Task::class)) {
            // BC with Laravel <9.22
            $regex = '/^Migrated:\s+2015_02_16_203700_CreateUsersTable\s/m';
        }
        $assertMethod = version_compare(Version::series(), '9.1', '>=') ? 'assertMatchesRegularExpression' : 'assertRegExp';
        $this->{$assertMethod}($regex, $app->getDisplay());

        $result = Db::select('select * from users');
        $this->assertCount(1, $result);
        $user = $result[0];
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('j.doe@example.com', $user->email);
        $this->assertEquals('pa$$word', $user->password);
    }
}
