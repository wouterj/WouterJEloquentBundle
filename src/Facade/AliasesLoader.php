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

/**
 * Lazy loads alias class.
 *
 * Based on the AliasLoader in the illuminate/foundation package.
 *
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class AliasesLoader
{
    private $aliases = [];

    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    public function load(string $class): void
    {
        $parts = explode('\\', $class);
        $alias = array_pop($parts);

        if ($this->hasAlias($alias)) {
            class_alias($this->getRealClass($alias), $class);
        }
    }

    public function addAlias(string $alias, string $class): void
    {
        $this->aliases[$alias] = $class;
    }

    protected function getRealClass(string $alias): string
    {
        return $this->aliases[$alias];
    }

    protected function hasAlias(string $alias): bool
    {
        return isset($this->aliases[$alias]);
    }
}
