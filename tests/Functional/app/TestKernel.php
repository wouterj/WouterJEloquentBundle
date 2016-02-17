<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \WouterJ\EloquentBundle\WouterJEloquentBundle(),
            new \AppBundle\AppBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'abc123',
        ]);

        $c->loadFromExtension('wouterj_eloquent', [
            'driver'   => 'sqlite',
            'database' => '%kernel.root_dir%/test.sqlite',
            'aliases'  => true,
        ]);
    }
}
