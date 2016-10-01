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

use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class Promise
{
    public static function containerHasService(ObjectProphecy $container, $service, $instance)
    {
        $container->has($service)->willReturn(true);
        $container->get($service)->willReturn($instance);
    }

    public static function containerDoesNotHaveService(ObjectProphecy $container, $service)
    {
        $container->has($service)->willReturn(false);
    }

    public static function containerHasParameter(ObjectProphecy $container, $name, $value)
    {
        $container->hasParameter($name)->willReturn(true);
        $container->getParameter($name)->willReturn($value);
    }

    public static function inputHasArgument(ObjectProphecy $input, $name, $value)
    {
        $input->hasArgument($name)->willReturn(true);
        $input->getArgument($name)->willReturn($value);
    }

    public static function inputHasOption(ObjectProphecy $input, $name, $value)
    {
        $input->hasOption($name)->willReturn(true);
        $input->getOption($name)->willReturn($value);
    }
}
