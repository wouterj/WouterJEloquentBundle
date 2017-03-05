<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Events;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Illuminate\Events\Dispatcher;

/**
 * A Laravel Events dispatcher using Symfony Dependency Injection.
 *
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class ServiceContainerDispatcher extends Dispatcher
{
    private $symfonyContainer;
    private $observers;

    public function __construct(ContainerInterface $symfonyContainer, array $observers)
    {
        $this->symfonyContainer = $symfonyContainer;
        $this->observers = $observers;

        parent::__construct();
    }

    protected function createClassCallable($listener)
    {
        list($class, $method) = $this->parseClassCallable($listener);

        if (!$this->handlerShouldBeQueued($class) && isset($this->observers[$class])) {
            return [$this->symfonyContainer->get($this->observers[$class]), $method];
        }

        return parent::createClassCallable($listener);
    }
}
