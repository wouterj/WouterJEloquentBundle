<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AppBundle\Controller\FormController;
use AppBundle\Model\UserObserver;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Log\Logger;

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
                'router' => ['resource' => __DIR__.'/routes.yml', 'utf8' => true],
                'validation' => ['enable_annotations' => true],
                'annotations' => true,
                'test'   => true,
                'form'   => true,
                'assets' => false,
            ]);

            $container->loadFromExtension('twig', [
                'paths' => [__DIR__.'/templates'],
                'exception_controller' => null,
                'strict_variables' => $container->getParameter('kernel.debug'),
            ]);

            $container->loadFromExtension('wouterj_eloquent', [
                'connections' => [
                    'default' => [
                        'driver'   => 'sqlite',
                        'database' => '%kernel.project_dir%/test.sqlite',
                    ],
                    'conn2' => [
                        'driver'   => 'sqlite',
                        'database' => '%kernel.project_dir%/test1.sqlite',
                    ],
                    'read_write' => [
                        'driver' => 'sqlite',
                        'read'   => ['database' => '%kernel.project_dir%/read.sqlite'],
                        'write'  => ['database' => '%kernel.project_dir%/write.sqlite'],
                    ],
                    'read_write_sticky' => [
                        'driver' => 'sqlite',
                        'sticky' => true,
                        'read'   => ['database' => '%kernel.project_dir%/read.sqlite'],
                        'write'  => ['database' => '%kernel.project_dir%/write.sqlite'],
                    ],
                ],
                'aliases'  => true,
                'eloquent' => true,
            ]);

            $container->register('app.user_observer', UserObserver::class)
                ->addTag('wouterj_eloquent.observer')
                ->setPublic(true);
            $container->register(FormController::class, FormController::class)
                ->addTag('controller.service_arguments')
                ->setPublic(true)
                ->setAutowired(true);

            if (class_exists(Logger::class)) {
                $container->register('logger', Logger::class)
                    ->setArguments([null, '/dev/null']);
            }
        });
    }
}
