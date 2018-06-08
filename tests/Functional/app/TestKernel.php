<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AppBundle\Model\UserObserver;
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
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new WouterJ\EloquentBundle\WouterJEloquentBundle(),
            new AppBundle\AppBundle(),
        ];
    }

    public function getProjectDir()
    {
        return __DIR__;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'secret' => 'abc123',
                'router' => ['resource' => __DIR__.'/routes.yml'],
                'templating' => (Kernel::MAJOR_VERSION < 2 ? ['engines' => ['twig']] : false),
                'validation' => ['enable_annotations' => true],
                'annotations' => true,
                'test'   => true,
                'form'   => true,
                'assets' => false,
            ]);

            $container->loadFromExtension('twig', [
                'paths' => [__DIR__.'/templates'],
            ]);

            $container->loadFromExtension('wouterj_eloquent', [
                'connections' => [
                    'default' => [
                        'driver'   => 'sqlite',
                        'database' => '%kernel.root_dir%/test.sqlite',
                    ],
                    'conn2' => [
                        'driver'   => 'sqlite',
                        'database' => '%kernel.root_dir%/test1.sqlite'
                    ],
                ],
                'aliases'  => true,
                'eloquent' => true,
            ]);

            $container->register('app.user_observer', UserObserver::class)
                ->addTag('wouterj_eloquent.observer')
                ->setPublic(true);
        });
    }
}
