<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Event;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class BootingModelEvent extends Event
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    public function getModel()
    {
        return clone $this->model;
    }
    
    public function __clone()
    {
        $this->model = clone $this->model;
    }
}
