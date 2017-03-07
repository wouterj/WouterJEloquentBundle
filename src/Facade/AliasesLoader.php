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

    public function __construct($aliases = [])
    {
        $this->aliases = $aliases;
    }

    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }

    public function load($class)
    {
        $parts = explode('\\', $class);
        $alias = array_pop($parts);

        if ($this->hasAlias($alias)) {
            class_alias($this->getRealClass($alias), $class);
        }
    }

    public function addAlias($alias, $class)
    {
        $this->aliases[$alias] = $class;
    }

    protected function getRealClass($alias)
    {
        return $this->aliases[$alias];
    }

    protected function hasAlias($alias)
    {
        return isset($this->aliases[$alias]);
    }
}
