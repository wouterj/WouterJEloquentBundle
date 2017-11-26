<?php

namespace WouterJ\EloquentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@amber.team>
 */
class SymfonyBackwardsCompatibilityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('kernel.project_dir')) {
            return;
        }

        foreach (['project_path', 'app_seeder_path'] as $param) {
            $container->setParameter(
                'wouterj_eloquent.'.$param,
                str_replace(
                    '%kernel.project_dir%',
                    '%kernel.root_dir%/../',
                    $container->getParameter('wouterj_eloquent.'.$param)
                )
            );
        }
    }
}
