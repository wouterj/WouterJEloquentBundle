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

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Test;

if (class_exists('PHPUnit_Runner_Version') && version_compare(\PHPUnit_Runner_Version::id(), '6.0.0', '<')) {
    class FunctionalTestListener extends \PHPUnit_Framework_BaseTestListener
    {
        private $listener;

        public function __construct()
        {
            $this->listener = new _FunctionalTestListener();
        }

        public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
        {
            $this->listener->startTestSuite($suite);
        }

        public function startTest(\PHPUnit_Framework_Test $test)
        {
            $this->listener->startTest($test);
        }
    }
} else {
    class FunctionalTestListener extends BaseTestListener
    {
        private $listener;

        public function __construct()
        {
            $this->listener = new _FunctionalTestListener();
        }

        public function startTestSuite(TestSuite $suite)
        {
            $this->listener->startTestSuite($suite);
        }

        public function startTest(Test $test)
        {
            $this->listener->startTest($test);
        }
    }
}

/**
 * Automatically creates the database that's
 * required for the functional tests.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class _FunctionalTestListener
{
    private static $started = false;
    private static $dbFile;
    private static $backupFile;

    public function startTestSuite($suite)
    {
        if (!self::$started) {
            self::$started = true;
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
                echo "Could not set-up the database:\n".implode("\n", $output);
                exit(1);
            }

            copy(static::$dbFile, static::$backupFile);

            if (file_exists(__DIR__.'/Functional/app/test1.sqlite')) {
                unlink(__DIR__.'/Functional/app/test1.sqlite');
            }
            touch(__DIR__.'/Functional/app/test1.sqlite');
        }
    }

    public function startTest($test)
    {
        // reset to initial file
        copy(static::$backupFile, static::$dbFile);
    }
}
