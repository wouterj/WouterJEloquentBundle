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

        $capsule = new Capsule();

        foreach ($config['connections'] as $name => $connection) {
            $capsule->addConnection($connection, $name);
        }
        $capsule->container['config']['database.default'] = $config['default_connection'];

        $container->set('wj_eloquent', $capsule);
        $container->set('wj_eloquent.database_manager', $capsule->getDatabaseManager());

        $this->capsuleEnabled = true;
    }

    protected function loadEloquent(array $config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config['eloquent'])) {
            return;
        }

        if (!$this->capsuleEnabled) {
            throw new \LogicException('There should be at least one connection configured on "wj_eloquent.connections" in order to use the Eloquent ORM.');
        }

        $container->get('wj_eloquent')->bootEloquent();
    }
}
