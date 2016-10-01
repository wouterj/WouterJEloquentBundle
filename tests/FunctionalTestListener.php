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

/**
 * Automatically creates the database that's
 * required for the functional tests.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FunctionalTestListener extends \PHPUnit_Framework_BaseTestListener
{
    private static $started = false;
    private static $dbFile;
    private static $backupFile;

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
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
                die("Could not set-up the database:\n".implode("\n", $output));
            }

            copy(static::$dbFile, static::$backupFile);
        }
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
        // reset to initial file
        copy(static::$backupFile, static::$dbFile);
    }
}
