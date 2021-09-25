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

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class ObserverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('wouterj_eloquent.events')) {
            return;
        }

        $definition = $container->getDefinition('wouterj_eloquent.events');
        $services = $container->findTaggedServiceIds('wouterj_eloquent.observer');

        $observers = [];
        foreach ($services as $id => $attrs) {
            $observers[$container->getDefinition($id)->getClass()] = new ServiceClosureArgument(new Reference($id));
        }

        $definition->replaceArgument(0, (new Definition(ServiceLocator::class, [$observers]))->addTag('container.service_locator'));
    }
}
