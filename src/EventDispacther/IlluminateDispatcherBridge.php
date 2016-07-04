<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WouterJ\EloquentBundle\Event\BootingModelEvent;

/**
 * Partial bridge to the Illuminate Event Dispatcher. It only implements the methods required for the booting of
 * Eloquent Models.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class IlluminateDispatcherBridge
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Fire an event until the first non-null response is returned.
     *
     * @param  string $event
     * @param  array  $payload
     *
     * @return mixed
     */
    public function until($event, $payload = [])
    {
        if ('eloquent.booting' !== substr($event, 0, 16)) {
            return;
        }
        
        $this->dispatcher->dispatch('eloquent.booting', new BootingModelEvent($payload));
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param string|object $event
     * @param mixed         $payload
     * @param bool          $halt
     *
     * @return array|null
     */
    public function fire($event, $payload = [], $halt = false)
    {
        // Do nothing
    }

    public function __clone()
    {
        $this->dispatcher = clone $this->dispatcher;
    }
}
