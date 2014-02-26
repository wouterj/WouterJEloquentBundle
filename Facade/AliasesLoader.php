<?php

namespace Wj\EloquentBundle\Facade;

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
