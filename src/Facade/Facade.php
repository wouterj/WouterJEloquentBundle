<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Facade;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The base Facade class.
 *
 * This class is based on the Facade class in the illuminate/support package.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class Facade
{
    /** @var null|ContainerInterface */
    protected static $container;
    /** @var object[] */
    protected static $facadeInstances = [];

    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Resolves the provided accessor into an object instance.
     *
     * @param object|string $accessor
     *
     * @return object
     */
    private static function resolveFacadeInstance($accessor)
    {
        if (is_object($accessor)) {
            return $accessor;
        }

        if (isset(static::$facadeInstances[$accessor])) {
            return static::$facadeInstances[$accessor];
        }

        if (static::$container->has($accessor)) {
            /** @var object $service */
            $service = static::$container->get($accessor);

            return static::$facadeInstances[$accessor] = $service;
        }

        throw new \LogicException(sprintf('Unknown facade accessor "%s"', print_r($accessor, true)));
    }

    /**
     * {@inheritDoc}
     */
    public static function __callStatic($method, array $parameters)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        return call_user_func_array([$instance, $method], $parameters);
    }

    /**
     * Returns the facade accessor.
     *
     * This can either be an object or a string containing the service id.
     *
     * @return object|string
     *
     * @throws \LogicException When not overriden by a child facade
     */
    protected static function getFacadeAccessor()
    {
        throw new \LogicException('The facade needs to override the getFacadeAccessor method');
    }
}
