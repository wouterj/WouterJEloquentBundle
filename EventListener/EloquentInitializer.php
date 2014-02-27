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

    /**
     * @param Capsule $capsule
     */
    public function __construct($capsule)
    {
        $this->setCapsule($capsule);
    }

    /**
     * Initializes the Eloquent ORM.
     */
    public function initialize()
    {
        $this->getCapsule()->bootEloquent();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'initialize',
        );
    }

    protected function getCapsule()
    {
        return $this->capsule;
    }

    private function setCapsule(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }
}
