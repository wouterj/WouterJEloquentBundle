<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection\Configurator;

use WouterJ\EloquentBundle\EventDispatcher\IlluminateDispatcherBridge;
use WouterJ\EloquentBundle\Model\FakeModel;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class IlluminateDispatcherBridgeConfigurator
{
    public function configure(IlluminateDispatcherBridge $dispatcher)
    {
        FakeModel::setBridgeEventDispatcher($dispatcher);
    }
}
