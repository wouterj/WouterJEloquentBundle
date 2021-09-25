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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('wouterj_eloquent');
        /** @psalm-suppress RedundantCondition */
        if (method_exists($treeBuilder, 'getRootNode')) {
            $root = $treeBuilder->getRootNode();
        } else {
            /** @psalm-suppress UndefinedMethod */
            $root = $treeBuilder->root('wouterj_eloquent');
        }

        $this->addAliasesSection($root);
        $this->addCapsuleSection($root);
        $this->addEloquentSection($root);

        return $treeBuilder;
    }

    protected function addAliasesSection($node): void
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

    /**
     * @param ArrayNodeDefinition|NodeDefinition $node
     */
    protected function addCapsuleSection($node): void
    {
        $node
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return is_array($v)
                        && !array_key_exists('connections', $v) && !array_key_exists('connection', $v)
                        && count($v) !== count(array_diff(array_keys($v), ['driver', 'host', 'port', 'database', 'username', 'password', 'charset', 'collation', 'prefix', 'read', 'write', 'sticky', 'schema']));
                })
                ->then(function ($v) {
                    // Key that should be rewritten to the connection config
                    $includedKeys = ['driver', 'host', 'port', 'database', 'username', 'password', 'charset', 'collation', 'prefix', 'read', 'write', 'sticky', 'schema'];
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
                            ->scalarNode('port')->defaultNull()->end()
                            ->scalarNode('database')->defaultNull()->end()
                            ->scalarNode('username')->defaultValue('root')->end()
                            ->scalarNode('password')->defaultValue('')->end()
                            ->scalarNode('charset')->defaultValue('utf8')->end()
                            ->scalarNode('collation')->defaultValue('utf8_unicode_ci')->end()
                            ->scalarNode('schema')->end()
                            ->booleanNode('sticky')->end()
                            ->scalarNode('prefix')->defaultValue('')->end()
                            ->arrayNode('write')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return $v['host'] === [];
                                    })
                                    ->then(function ($v) {
                                        unset($v['host']);

                                        return $v;
                                    })
                                ->end()
                                ->children()
                                    ->arrayNode('host')
                                        ->beforeNormalization()
                                            ->ifString()->then(function ($v) { return [$v]; })
                                        ->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->scalarNode('port')->end()
                                    ->scalarNode('database')->end()
                                    ->scalarNode('username')->end()
                                    ->scalarNode('password')->end()
                                    ->scalarNode('charset')->end()
                                    ->scalarNode('collation')->end()
                                    ->scalarNode('prefix')->end()
                                ->end()
                            ->end() // write
                            ->arrayNode('read')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return $v['host'] === [];
                                    })
                                    ->then(function ($v) {
                                        unset($v['host']);

                                        return $v;
                                    })
                                ->end()
                                ->children()
                                    ->arrayNode('host')
                                        ->beforeNormalization()
                                            ->ifString()->then(function ($v) { return [$v]; })
                                        ->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->scalarNode('port')->end()
                                    ->scalarNode('database')->end()
                                    ->scalarNode('username')->end()
                                    ->scalarNode('password')->end()
                                    ->scalarNode('charset')->end()
                                    ->scalarNode('collation')->end()
                                    ->scalarNode('prefix')->end()
                                ->end()
                            ->end() // read
                        ->end()
                    ->end()
                ->end() // connections
            ->end();
    }

    protected function addEloquentSection($node): void
    {
        $node
            ->children()
                ->arrayNode('eloquent')
                    ->canBeEnabled()
                ->end()
            ->end();
    }
}
