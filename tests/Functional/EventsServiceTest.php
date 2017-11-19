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
class EventsServiceTest extends EventsTest
{
    private $userObserver;

    protected function reset()
    {
        if (null === $this->userObserver) {
            $this->userObserver = new UserObserver();
            static::$kernel->getContainer()->set('app.user_observer', $this->userObserver);
        }

        $this->userObserver->fired = [];
    }

    protected function getLogs()
    {
        return $this->userObserver->fired;
    }
}
