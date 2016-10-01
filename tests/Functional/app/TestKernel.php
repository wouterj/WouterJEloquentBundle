<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class TestKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \WouterJ\EloquentBundle\WouterJEloquentBundle(),
            new \AppBundle\AppBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'secret' => 'abc123',
            ]);

            $container->loadFromExtension('wouterj_eloquent', [
                'driver'   => 'sqlite',
                'database' => '%kernel.root_dir%/test.sqlite',
                'aliases'  => true,
                'eloquent' => true,
            ]);
        });
    }
}
