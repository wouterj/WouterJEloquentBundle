<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrationPathsPass implements CompilerPassInterface
{
    private static $paths = [];

    public static function add(string $path): void
    {
        static::$paths[] = $path;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('wouterj_eloquent.migrator')) {
            return;
        }

        $definition = $container->getDefinition('wouterj_eloquent.migrator');
        foreach (static::$paths as $path) {
            $definition->addMethodCall('path', [$path]);
        }
    }
}
