<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2020 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class EloquentUserProviderFactory implements UserProviderFactoryInterface
{
    public function getKey(): string
    {
        return 'eloquent';
    }

    public function addConfiguration(NodeDefinition $builder): void
    {
        /** @var ArrayNodeDefinition $builder */
        $builder
            ->info('Configures a user provider using the Eloquent ORM')
            ->children()
                ->scalarNode('model')->cannotBeEmpty()->info('The FQCN of your user model.')->end()
                ->scalarNode('attribute')->cannotBeEmpty()->info('The attribute of your user model to use as username.')->end()
            ->end()
        ;
    }

    public function create(ContainerBuilder $container, $id, $config): void
    {
        $container->setDefinition($id, new Definition(EloquentUserProvider::class))
            ->addArgument($config['model'])
            ->addArgument($config['attribute'])
        ;
    }
}
