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

use PSR\Container\ContainerInterface as PsrContainerInterface;
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

    public function __construct(/* PsrContainerInterface */$symfonyContainer, array $observers = [])
    {
        if (!$symfonyContainer instanceof ContainerInterface && !$symfonyContainer instanceof PsrContainerInterface) {
            throw new \InvalidArgumentException('Argument 1 of '.__CLASS__.' has to be instance of '.ContainerInterface::class.' or '.PsrContainerInterface::class.', '.get_class($symfonyContainer).' given.');
        }

        if ([] !== $observers) {
            @trigger_error('Passing an array as second argument to '.__CLASS__.' is deprecated since 1.0 and will be removed in 2.0.', \E_USER_DEPRECATED);
        }

        $this->symfonyContainer = $symfonyContainer;
        $this->observers = $observers;

        parent::__construct();
    }

    protected function createClassCallable($listener)
    {
        list($class, $method) = $this->parseClassCallable($listener);

        if (!$this->handlerShouldBeQueued($class)) {
            if ($this->symfonyContainer->has($class)) {
                return [$this->symfonyContainer->get($class), $method];
            } elseif (isset($this->observers[$class])) {
                return [$this->symfonyContainer->get($this->observers[$class]), $method];
            }
        }

        return parent::createClassCallable($listener);
    }
}
