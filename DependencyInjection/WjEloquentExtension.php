<?php

namespace Wj\EloquentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Illuminate\Database\Capsule\Manager as Capsule;

class WjEloquentExtension extends Extension
{
    private $capsuleEnabled = false;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadCapsule($config, $container, $loader);
        $this->loadEloquent($config, $container);
    }

    protected function loadCapsule(array $config, ContainerBuilder $container, Loader\XmlFileLoader $loader)
    {
        if (0 === count($config['connections'])) {
            return;
        }

        $loader->load('services.xml');

        $capsuleDefinition = $container->getDefinition('wj_eloquent');
        foreach ($config['connections'] as $name => $connection) {
            $capsuleDefinition->addMethodCall('addConnection', array($connection, $name));
        }

        if ('default' !== $config['default_connection']) {
            $container->getDefinition('wj_eloquent.database_manager')->addMethodCall('setDefaultConnection', array($config['default_connection']));
        }
    }

    protected function loadEloquent(array $config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config['eloquent'])) {
            return;
        }

        if (!$container->hasDefinition('wj_eloquent')) {
            throw new \LogicException('There should be at least one connection configured on "wj_eloquent.connections" in order to use the Eloquent ORM.');
        }

        $container->getDefinition('wj_eloquent')->addMethodCall('bootEloquent');
    }

    public function getNamespace()
    {
        return 'http://wouterj.nl/schema/dic/eloquent';
    }
}
