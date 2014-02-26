<?php

namespace Wj\EloquentBundle\Facade;

use Symfony\Component\DependencyInjection\Container;

abstract class Facade
{
    /** @var Container */
    protected static $container;
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
     */
    protected static function getFacadeAccessor()
    {
        throw new \LogicException('The facade needs to override the getFacadeAccessor method');
    }
}
