<?php

namespace WouterJ\EloquentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('wj_eloquent');

        $this->addAliasesSection($root);
        $this->addCapsuleSection($root);
        $this->addEloquentSection($root);

        return $treeBuilder;
    }

    protected function addAliasesSection($node)
    {
        $node
            ->children()
                ->arrayNode('aliases')
                    ->beforeNormalization()
                        ->always()
                        ->then(function ($v) {
                            if (is_bool($v)) {
                                $u = array();
                                $u['db'] = $v;
                                $u['schema'] = $v;

                                return $u;
                            }

                            if (isset($v['enabled'])) {
                                $v['db'] = $v['enabled'];
                                $v['schema'] = $v['enabled'];
                                unset($v['enabled']);

                                return $v;
                            }

                            if (null === $v) {
                                $v = array();
                                $v['db'] = true;
                                $v['schema'] = true;

                                return $v;
                            }

                            return $v;
                        })
                    ->end()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('db')->defaultFalse()->end()
                        ->booleanNode('schema')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addCapsuleSection($node)
    {
        $node
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return is_array($v)
                        && !array_key_exists('connections', $v) && !array_key_exists('connection', $v)
                        && count($v) !== count(array_diff(array_keys($v), array('driver', 'host', 'database', 'username', 'password', 'charset', 'collation', 'prefix')));
                })
                ->then(function ($v) {
                    // Key that should be rewritten to the connection config
                    $includedKeys = array('driver', 'host', 'database', 'username', 'password', 'charset', 'collation', 'prefix');
                    $connection = array();
                    foreach ($v as $key => $value) {
                        if (in_array($key, $includedKeys)) {
                            $connection[$key] = $v[$key];
                            unset($v[$key]);
                        }
                    }
                    $v['default_connection'] = isset($v['default_connection']) ? $v['default_connection'] : 'default';
                    $v['connections'] = array($v['default_connection'] => $connection);

                    return $v;
                })
            ->end()
            ->fixXmlConfig('connection')
            ->children()
                ->scalarNode('default_connection')->defaultNull()->end()
                ->arrayNode('connections')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function ($v) {
                                return array('database' => $v);
                            })
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('driver')
                                ->validate()
                                    ->ifNotInArray(array('mysql', 'postgres', 'sql server', 'sqlite'))
                                    ->thenInvalid('Invalid database driver "%s".')
                                ->end()
                                ->defaultValue('mysql')
                            ->end()
                            ->scalarNode('host')->defaultValue('localhost')->end()
                            ->scalarNode('database')->isRequired()->end()
                            ->scalarNode('username')->defaultValue('root')->end()
                            ->scalarNode('password')->defaultValue('')->end()
                            ->scalarNode('charset')->defaultValue('utf8')->end()
                            ->scalarNode('collation')->defaultValue('utf8_unicode_ci')->end()
                            ->scalarNode('prefix')->defaultValue('')->end()
                        ->end()
                    ->end()
                ->end() // connections
            ->end();
    }

    protected function addEloquentSection($node)
    {
        $node
            ->children()
                ->arrayNode('eloquent')
                    ->canBeEnabled()
                ->end()
            ->end();
    }
}
