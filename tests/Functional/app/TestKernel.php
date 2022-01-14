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
use AppBundle\Model\User;
use AppBundle\Model\UserObserver;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorageFactory;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class TestKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new WouterJ\EloquentBundle\WouterJEloquentBundle(),
            new AppBundle\AppBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $sessionConfig = ['storage_factory_id' => 'session.storage.factory.mock_file'];
            if (!class_exists(MockFileSessionStorageFactory::class)) {
                // BC with symfony/http-foundation <5.3
                $sessionConfig = ['storage_id' => 'session.storage.mock_file'];
            }

            $container->loadFromExtension('framework', [
                'secret' => 'abc123',
                'router' => ['resource' => __DIR__.'/routes.yml', 'utf8' => true],
                'validation' => ['enable_annotations' => true],
                'annotations' => true,
                'test'   => true,
                'form'   => true,
                'assets' => false,
                'session' => $sessionConfig,
                'csrf_protection' => false,
                'property_access' => true,
            ]);

            $container->loadFromExtension('twig', [
                'paths' => [__DIR__.'/templates'],
                'exception_controller' => null,
                'strict_variables' => $container->getParameter('kernel.debug'),
            ]);

            $securityConfig = [
                'providers' => [
                    'test' => [
                        'eloquent' => ['model' => User::class, 'attribute' => 'email'],
                    ],
                ],
                'firewalls' => [
                    'main' => ['pattern' => '^/secured/', 'http_basic' => true],
                ],
                'password_hashers' => [User::class => 'plaintext'],
            ];
            if (class_exists(AuthenticatorManager::class)) {
                $securityConfig['enable_authenticator_manager'] = true;
            }
            if (!interface_exists(PasswordHasherInterface::class)) {
                $securityConfig['encoders'] = $securityConfig['password_hashers'];
                unset($securityConfig['password_hashers']);
            }
            $container->loadFromExtension('security', $securityConfig);

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
