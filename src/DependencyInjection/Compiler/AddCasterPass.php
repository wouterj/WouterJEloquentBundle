<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Illuminate\Database\Eloquent\Model;
use WouterJ\EloquentBundle\VarDumper\EloquentCaster;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class AddCasterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('var_dumper.cloner')) {
            return;
        }

        $definition = $container->getDefinition('var_dumper.cloner');
        $definition->addMethodCall('addCasters', [[
            Model::class => [EloquentCaster::class, 'castModel']
        ]]);
    }
}
