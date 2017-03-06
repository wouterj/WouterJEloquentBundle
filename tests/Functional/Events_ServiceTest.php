<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\UserObserver;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class Events_ServiceTest extends EventsTest
{
    protected function reset()
    {
        static::$kernel->getContainer()->get('app.user_observer')->fired = [];
    }

    protected function getLogs()
    {
        return static::$kernel->getContainer()->get('app.user_observer')->fired;
    }
}
