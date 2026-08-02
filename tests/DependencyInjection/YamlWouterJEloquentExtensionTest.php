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
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlWouterJEloquentExtensionTest extends WouterJEloquentExtensionTestCase
{
    protected function loadConfig(ContainerBuilder $container, $name)
    {
        (new YamlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config')))->load($name.'.yml');
    }
}
