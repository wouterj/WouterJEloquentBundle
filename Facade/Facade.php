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

use Symfony\Component\DependencyInjection\Container;

/**
 * The base Facade class.
 *
 * This class is based on the Facade class in the illuminate/support package.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
abstract class Facade
{
    /** @var Container */
    protected static $container;
    /** @var object[] */
    protected static $facadeInstances = array();

    public static function setContainer(Container $container)
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
            return static::$facadeInstances[$accessor] = static::$container->get($accessor);
        }

        throw new \LogicException(sprintf('Unknown facade accessor "%s"', print_r($accessor, true)));
    }

    /**
     * {@inheritDoc}
     */
    public static function __callStatic($method, array $parameters)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        return call_user_func_array(array($instance, $method), $parameters);
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
