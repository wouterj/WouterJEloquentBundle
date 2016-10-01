<?php

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

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if (!self::$started) {
            self::$started = true;
            $cmdPrefix = 'php "'.__DIR__.'/Functional/app/bin/console"';

            exec($cmdPrefix.' cache:clear');

            $dbFile = __DIR__.'/Functional/app/test.sqlite';
            if (file_exists($dbFile)) {
                unlink($dbFile);
            }

            // set up database
            touch($dbFile);
            exec($cmdPrefix.' eloquent:migrate:install', $output);
            if (false === strpos(current(array_filter($output)), 'successfully')) {
                die("Could not set-up the database:\n".implode("\n", $output));
            }
        }
    }
}
