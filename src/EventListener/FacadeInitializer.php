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

/**
 * Initializes the facades.
 *
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class FacadeInitializer
{
    /** @var null|AliasesLoader */
    private $loader;
    /** @var ContainerInterface */
    private $container;

    /** @psalm-suppress ContainerDependency */
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

    public function setLoader(AliasesLoader $loader)
    {
        $this->loader = $loader;
    }
}
