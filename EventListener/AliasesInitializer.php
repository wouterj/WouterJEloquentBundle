<?php

namespace Wj\EloquentBundle\EventListener;

use Wj\EloquentBundle\Facade\AliasesLoader;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Initializes the aliases loader.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class AliasesInitializer implements EventSubscriberInterface
{
    /** @var AliasesLoader */
    private $loader;

    /**
     * @param AliasesLoader $loader
     */
    public function __construct($loader)
    {
        $this->setLoader($loader);
    }

    public function initialize()
    {
        $this->getLoader()->register();
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'initialize',
        );
    }

    protected function getLoader()
    {
        return $this->loader;
    }

    private function setLoader(AliasesLoader $loader)
    {
        $this->loader = $loader;
    }
}
