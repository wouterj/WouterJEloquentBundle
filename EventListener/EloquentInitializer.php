<?php

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

    public function initialize()
    {
        $this->getCapsule()->bootEloquent();
    }

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
