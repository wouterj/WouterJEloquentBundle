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

            if (file_exists(__DIR__.'/Functional/app/test.sqlite')) {
                return;
            }

            // set up database
            touch(__DIR__.'/Functional/app/test.sqlite');
            exec('php "'.__DIR__.'/Functional/app/bin/console" eloquent:migrate:install');
        }
    }
}
