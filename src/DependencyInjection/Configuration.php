<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('wouterj_eloquent');

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
                            if (null === $v) {
                                return ['db' => true, 'schema' => true];
                            }

                            if (is_bool($v)) {
                                return ['db' => $v, 'schema' => $v];
                            }

                            if (isset($v['enabled'])) {
                                $v['db'] = $v['enabled'];
                                $v['schema'] = $v['enabled'];
                                unset($v['enabled']);

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
                        && count($v) !== count(array_diff(array_keys($v), ['driver', 'host', 'port', 'database', 'username', 'password', 'charset', 'collation', 'prefix']));
                })
                ->then(function ($v) {
                    // Key that should be rewritten to the connection config
                    $includedKeys = ['driver', 'host', 'port', 'database', 'username', 'password', 'charset', 'collation', 'prefix'];
                    $connection = [];
                    foreach ($v as $key => $value) {
                        if (in_array($key, $includedKeys)) {
                            $connection[$key] = $v[$key];
                            unset($v[$key]);
                        }
                    }
                    $v['default_connection'] = isset($v['default_connection']) ? $v['default_connection'] : 'default';
                    $v['connections'] = [$v['default_connection'] => $connection];

                    return $v;
                })
            ->end()
            ->fixXmlConfig('connection')
            ->children()
                ->scalarNode('default_connection')->defaultValue('default')->end()
                ->arrayNode('connections')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function ($v) {
                                return ['database' => $v];
                            })
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('driver')
                                ->validate()
                                    ->ifNotInArray(['mysql', 'postgres', 'pgsql', 'sql server', 'sqlsrv', 'sqlite'])
                                    ->thenInvalid('Invalid database driver "%s".')
                                ->end()
                                ->beforeNormalization()
                                    ->ifInArray(['sql server', 'postgres'])
                                    ->then(function ($value) {
                                        $names = ['sql server' => 'sqlsrv', 'postgres' => 'pgsql'];

                                        @trigger_error(sprintf(
                                            'Driver name "%s" is deprecated as of version 0.4 and will be removed in 1.0. Use "%s" instead.',
                                            $value,
                                            $names[$value]
                                        ), E_USER_DEPRECATED);

                                        return $names[$value];
                                    })
                                ->end()
                                ->defaultValue('mysql')
                            ->end()
                            ->scalarNode('host')->defaultValue('localhost')->end()
                            ->scalarNode('port')->defaultValue(NULL)->end()
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
