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

use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

#[Group('legacy')]
class XmlWouterJEloquentExtensionTest extends WouterJEloquentExtensionTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(XmlFileLoader::class)) {
            $this->markTestSkipped('XML no longer supported in Symfony');
        }

        parent::setUp();
    }

    protected function loadConfig(ContainerBuilder $container, $name)
    {
        (new XmlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config')))->load($name.'.xml');
    }
}
