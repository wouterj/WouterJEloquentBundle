<?php

namespace WouterJ\EloquentBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\HttpFoundation\Request;
use WouterJ\EloquentBundle\Facade\Db;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrationsTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/TestKernel.php';

        return 'TestKernel';
    }

    public function testRunningMigrations()
    {
        static::bootKernel();
        Db::setContainer($container = static::$kernel->getContainer());

        $app = new ApplicationTester($a = new Application(static::$kernel));
        $a->setAutoExit(false);

        // reset, in case tests were run previously
        $container->get('wouterj_eloquent.migrator')->reset();

        $app->run(['command' => 'eloquent:migrate', '--seed' => 'true'], ['decorated' => false]);

        $this->assertContains('Migrated: 2015_02_16_203700_CreateUsersTable', $app->getDisplay());

        $result = Db::select('select * from users');
        $this->assertCount(1, $result);
        $user = $result[0];
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('j.doe@example.com', $user->email);
        $this->assertEquals('pa$$word', $user->password);
    }
}
