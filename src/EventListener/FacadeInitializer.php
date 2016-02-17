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

use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Facade\Facade;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use Symfony\Component\Console\ConsoleEvents;
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
    /** @var ContainerInterface */
    private $container;
    private $run = false;

    /** {@inheritDoc} */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST  => 'initialize',
            ConsoleEvents::COMMAND => 'initializeConsole',
        ];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Configures the facades and registers the aliases loader, when
     * activated.
     */
    public function initialize()
    {
        Facade::setContainer($this->container);

        if (null !== $loader = $this->loader) {
            $loader->register();
        }
    }

    public function initializeConsole()
    {
        if ($this->run) {
            return;
        }

        $this->initialize();
        $this->run = true;
    }

    public function setLoader(AliasesLoader $loader)
    {
        $this->loader = $loader;
    }
}
