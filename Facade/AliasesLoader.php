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

class AliasesLoader
{
    private $aliases = array();

    /**
     * @param array     $aliases
     */
    public function __construct($aliases = array())
    {
        $this->setAliases($aliases);
    }

    public function register()
    {
        spl_autoload_register(array($this, 'load'));
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

    private function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
}
