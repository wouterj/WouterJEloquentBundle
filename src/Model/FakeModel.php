<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Model;

use Illuminate\Database\Eloquent\Model;
use WouterJ\EloquentBundle\EventDispatcher\IlluminateDispatcherBridge;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class FakeModel extends Model
{
    public function __construct()
    {
        throw new \DomainException('FakeModel should not be instantiated.');
    }
    
    public static function setBridgeEventDispatcher(IlluminateDispatcherBridge $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }
}
