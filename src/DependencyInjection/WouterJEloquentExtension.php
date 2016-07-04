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

use WouterJ\EloquentBundle\Facade\Facade;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritDoc}
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class WouterJEloquentExtension extends Extension
{
    private $capsuleEnabled = false;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../../resources/config'));

        $loader->load('migrations.xml');

        $this->loadCapsule($config, $container, $loader);
        $this->loadEloquent($config, $container, $loader);
        $this->loadFacades($config, $container, $loader);
    }

    protected function loadCapsule(array $config, ContainerBuilder $container, Loader\XmlFileLoader $loader)
    {
        if (0 === count($config['connections'])) {
            return;
        }

        $loader->load('services.xml');

        $capsuleDefinition = $container->getDefinition('wouterj_eloquent');
        foreach ($config['connections'] as $name => $connection) {
            $capsuleDefinition->addMethodCall('addConnection', [$connection, $name]);
        }

        $container->setParameter('wouterj_eloquent.default_connection', $config['default_connection']);
    }

    protected function loadEloquent(array $config, ContainerBuilder $container, Loader\XmlFileLoader $loader)
    {
        if (!$this->isConfigEnabled($container, $config['eloquent'])) {
            return;
        }

        if (!$container->hasDefinition('wouterj_eloquent')) {
            throw new \LogicException('There should be at least one connection configured on "wouterj_eloquent.connections" in order to use the Eloquent ORM.');
        }

        $loader->load('eloquent.xml');
    }

    protected function loadFacades(array $config, ContainerBuilder $container, Loader\XmlFileLoader $loader)
    {
        $loader->load('facades.xml');

        if ($config['aliases']['db'] || $config['aliases']['schema']) {
            $aliasesLoaderDefinition = $container->getDefinition('wouterj_eloquent.aliases.loader');
            if ($config['aliases']['db']) {
                $aliasesLoaderDefinition->addMethodCall('addAlias', ['DB', 'WouterJ\EloquentBundle\Facade\Db']);
            }
            if ($config['aliases']['schema']) {
                $aliasesLoaderDefinition->addMethodCall('addAlias', ['Schema', 'WouterJ\EloquentBundle\Facade\Schema']);
            }

            $container->getDefinition('wouterj_eloquent.facade.initializer')->addMethodCall('setLoader', [new Reference('wouterj_eloquent.aliases.loader')]);
        }
    }

    public function getNamespace()
    {
        return 'http://wouterj.nl/schema/dic/eloquent';
    }

    public function getAlias()
    {
        return 'wouterj_eloquent';
    }
}
