<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use PHPUnit\Event\Test\AfterTestMethodFinished;
use PHPUnit\Event\Test\AfterTestMethodFinishedSubscriber;
use PHPUnit\Event\Test\BeforeTestMethodCalled;
use PHPUnit\Event\Test\BeforeTestMethodCalledSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Automatically creates the database that's
 * required for the functional tests.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FunctionalTestExtension implements Extension
{
    private static $started = false;
    private static $dbFile;
    private static $backupFile;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(
            new class implements BeforeTestMethodCalledSubscriber
            {
                public function notify(BeforeTestMethodCalled $event): void
                {
                    FunctionalTestExtension::beforeTestMethodCalled();
                }
            }
        );

        $facade->registerSubscriber(
            new class implements AfterTestMethodFinishedSubscriber
            {
                public function notify(AfterTestMethodFinished $event): void
                {
                    FunctionalTestExtension::afterTestMethodFinished($event);
                }
            }
        );
    }

    public static function beforeTestMethodCalled(): void
    {
        if (self::$started) {
            return;
        }

        self::$started = true;
        self::createDb();
    }

    public static function afterTestMethodFinished(AfterTestMethodFinished $event): void
    {
        // BC PHPUnit 11 (required for PHP 8.2)
        $testClass = method_exists($event, 'test') ? $event->test()->className() : $event->testClassName();
        if (!is_a($testClass, KernelTestCase::class, true)) {
            return;
        }

        self::resetDb();
    }

    private static function createDb(): void
    {
        static::$dbFile = __DIR__.'/Functional/app/test.sqlite';
        static::$backupFile = __DIR__.'/Functional/app/_test.sqlite';

        // clear cache
        $cmdPrefix = 'php "'.__DIR__.'/Functional/app/bin/console"';
        exec($cmdPrefix.' cache:clear');

        // create initial db
        if (file_exists(static::$dbFile)) {
            unlink(static::$dbFile);
        }
        if (file_exists(static::$backupFile)) {
            unlink(static::$backupFile);
        }

        touch(static::$dbFile);
        exec($cmdPrefix.' eloquent:migrate:install', $output);
        if (false === strpos(implode("\n", $output), 'successfully')) {
            throw new \Exception("Could not set-up the database:\n".implode("\n", $output));
        }

        copy(static::$dbFile, static::$backupFile);

        if (file_exists(__DIR__.'/Functional/app/test1.sqlite')) {
            unlink(__DIR__.'/Functional/app/test1.sqlite');
        }
        touch(__DIR__.'/Functional/app/test1.sqlite');
    }

    public static function resetDb(): void
    {
        // reset to initial file
        copy(static::$backupFile, static::$dbFile);
    }
}
