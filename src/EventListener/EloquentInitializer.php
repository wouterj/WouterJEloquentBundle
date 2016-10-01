<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Initializes the Eloquent ORM.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class EloquentInitializer implements EventSubscriberInterface
{
    /** @var Capsule */
    private $capsule;
    private $run = false;

    /** {@inheritDoc} */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST  => 'initialize',
            ConsoleEvents::COMMAND => 'initializeConsole',
        ];
    }

    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    /**
     * Initializes the Eloquent ORM.
     */
    public function initialize()
    {
        $this->capsule->bootEloquent();
    }

    public function initializeConsole()
    {
        if ($this->run) {
            return;
        }

        $this->initialize();
        $this->run = true;
    }
}
