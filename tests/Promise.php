<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use Mockery\MockInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class Promise
{
    public static function containerHasService(MockInterface $container, $service, $instance)
    {
        $container->allows()->has()->with($service)->andReturn(true);
        $container->allows()->get()->with($service)->andReturn($instance);
    }

    public static function containerDoesNotHaveService(MockInterface $container, $service)
    {
        $container->allows()->has()->with($service)->andReturn(false);
    }

    public static function containerHasParameter(MockInterface $container, $name, $value)
    {
        $container->allows()->hasParameter()->with($name)->andReturn(true);
        $container->allows()->getParameter()->with($name)->andReturn($value);
    }

    public static function inputHasArgument(MockInterface $input, $name, $value)
    {
        $input->allows()->hasArgument()->with($name)->andReturn(true);
        $input->allows()->getArgument()->with($name)->andReturn($value);
    }

    public static function inputHasOption(MockInterface $input, $name, $value)
    {
        $input->allows()->hasOption()->with($name)->andReturn(true);
        $input->allows()->getOption()->with($name)->andReturn($value);
    }
}
