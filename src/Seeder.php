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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Database\ConnectionInterface;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class Seeder extends BaseSeeder
{
    /** @var null|ContainerInterface */
    protected $container;
    protected $seededClasses = [];
    /**
     * @var ConnectionInterface|null
     * @deprecated since 2.3
     */
    protected $connection;

    /** @return mixed */
    public function __invoke(array $parameters = [])
    {
        return $this->run(...$parameters);
    }

    /** @return $this */
    public function call($class, $silent = false, array $parameters = [])
	{
	    $classes = is_array($class) ? $class : [$class];

	    foreach ($classes as $class) {
            $seeder = $this->resolve($class);
            if (null !== $this->connection) {
                $seeder->setConnection($this->connection, false);
            }

            ($seeder)($parameters);
        }

	    return $this;
    }

    public function resolve($class): Seeder
    {
        if ($this->getContainer()->has($class)) {
            $seeder = $this->getContainer()->get($class);
        } else {
            $seeder = new $class;
        }

        if (!$seeder instanceof self) {
            throw new \LogicException(sprintf('The seeder "%s" does not extend WouterJ\EloquentBundle\Seeder', get_class($seeder)));
        }
        $seeder->setSfContainer($this->getContainer());

        $this->addSeededClass($seeder);

        return $seeder;
    }

    public function setSfContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    protected function getContainer(): ContainerInterface
    {
        if (null === $this->container) {
            throw new \LogicException(sprintf('"%1$s::$container" must not be null, did you forget to inject the container using "%1$s::setSfContainer()"?', __CLASS__));
        }

        return $this->container;
    }

    /** @return string[] */
    public function getSeedClasses(): array
    {
        return $this->seededClasses;
    }

    /** @param object|string $object */
    protected function addSeededClass($object): void
    {
        $this->seededClasses[] = is_string($object) ? $object : get_class($object);
    }

    /** @deprecated since 2.3 */
    public function setConnection(ConnectionInterface $connection/*, $triggerDeprecation = true*/): void
    {
        if (1 === func_num_args() || func_get_arg(1)) {
            trigger_deprecation('wouterj/eloquent-bundle', '2.3', 'The "%s()" method is deprecated.', __METHOD__);
        }

        $this->connection = $connection;
    }
}
