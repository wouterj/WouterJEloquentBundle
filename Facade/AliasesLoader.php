<?php

namespace Wj\EloquentBundle\Facade;

class AliasesLoader
{
    private $aliases = array();

    /**
     * @param array     $aliases
     */
    public function __construct($aliases)
    {
        $this->setAliases($aliases);
    }

    public function register()
    {
        spl_autoload_register(array($this, 'load'));
    }

    public function load($alias)
    {
        if ($this->hasAlias($alias)) {
            class_alias($this->getRealClass($alias), $alias);
        }
    }

    protected function getRealClass($alias)
    {
        return $this->aliases[$alias];
    }

    protected function hasAlias($alias)
    {
        return isset($this->aliases[$class]);
    }

    private function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
}
