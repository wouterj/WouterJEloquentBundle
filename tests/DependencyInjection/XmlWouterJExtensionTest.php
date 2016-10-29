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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use WouterJ\EloquentBundle\EventListener\EloquentInitializer;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseManager;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class XmlWouterJEloquentExtensionTest extends WouterJEloquentExtensionTest
{
    protected function loadConfig(ContainerBuilder $container, $name)
    {
        (new XmlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config')))->load($name.'.xml');
    }
}
