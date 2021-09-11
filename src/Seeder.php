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
    /** @var ConnectionInterface */
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
            $seeder->setConnection($this->connection);

            if (false === $silent && isset($this->command)) {
                $this->command->getOutput()->writeln("<info>Seeding:</info> $class");
            }

            $startTime = microtime(true);

            ($seeder)($parameters);

            $runTime = round(microtime(true) - $startTime, 2);

            if ($silent === false && isset($this->command)) {
                $this->command->getOutput()->writeln("<info>Seeded:</info>  $class ($runTime seconds)");
            }
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

    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }
}
