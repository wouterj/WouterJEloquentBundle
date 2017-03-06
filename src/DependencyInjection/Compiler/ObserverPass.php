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

/**
 * {@inheritDoc}
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class ObserverPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wouterj_eloquent.events')) {
            return;
        }

        $definition = $container->getDefinition('wouterj_eloquent.events');
        $services = $container->findTaggedServiceIds('wouterj_eloquent.observer');

        $observers = [];
        foreach ($services as $id => $attrs) {
            $observers[$container->getDefinition($id)->getClass()] = $id;
        }

        $definition->replaceArgument(1, $observers);
    }
}
