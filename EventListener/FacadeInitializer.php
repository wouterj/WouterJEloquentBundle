<?php

namespace Wj\EloquentBundle\EventListener;

use Wj\EloquentBundle\Facade\Facade;
use Wj\EloquentBundle\Facade\AliasesLoader;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Initializes the facades.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class FacadeInitializer implements EventSubscriberInterface
{
    /** @var null|AliasesLoader */
    private $loader;
    /** @var Container */
    private $container;

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'initialize',
        );
    }

    /**
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->setContainer($container);
    }

    public function initialize()
    {
        Facade::setContainer($this->getContainer());

        if (null !== $loader = $this->getLoader()) {
            $loader->register();
        }
    }

    protected function getLoader()
    {
        return $this->loader;
    }

    public function setLoader(AliasesLoader $loader)
    {
        $this->loader = $loader;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    private function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
