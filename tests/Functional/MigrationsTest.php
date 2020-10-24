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
    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    public function testRunningMigrations()
    {
        static::bootKernel();

        $app = new ApplicationTester($a = new Application(static::$kernel));
        $a->setAutoExit(false);

        // reset, in case tests were run previously
        $app->run(['command' => 'eloquent:migrate:reset'], ['decorated' => false]);

        $app->run(['command' => 'eloquent:migrate', '--seed' => true], ['decorated' => false]);

        $assertMethod = version_compare(Version::series(), '9.1', '>=') ? 'assertMatchesRegularExpression' : 'assertRegExp';
        $this->{$assertMethod}('/^Migrated:\s+2015_02_16_203700_CreateUsersTable\s/m', $app->getDisplay());

        $result = Db::select('select * from users');
        $this->assertCount(1, $result);
        $user = $result[0];
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('j.doe@example.com', $user->email);
        $this->assertEquals('pa$$word', $user->password);
    }
}
