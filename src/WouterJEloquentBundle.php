<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WouterJ\EloquentBundle\DependencyInjection\Compiler\AddCasterPass;
use WouterJ\EloquentBundle\DependencyInjection\Compiler\ObserverPass;
use WouterJ\EloquentBundle\DependencyInjection\Compiler\SymfonyBackwardsCompatibilityPass;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @final
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class WouterJEloquentBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DependencyInjection\WouterJEloquentExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ObserverPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 80);
        $container->addCompilerPass(new SymfonyBackwardsCompatibilityPass());
        $container->addCompilerPass(new AddCasterPass());
    }

    public function boot()
    {
        if ($this->container->has('wouterj_eloquent.initializer')) {
            $this->container->get('wouterj_eloquent.initializer')->initialize();
        }

        if ($this->container->has('wouterj_eloquent.facade.initializer')) {
            $this->container->get('wouterj_eloquent.facade.initializer')->initialize();
        }

        if (class_exists(AnnotationReader::class)) {
            AnnotationReader::addGlobalIgnoredName('mixin');
        }
    }

    public function getPath()
    {
        return dirname(__DIR__);
    }
}
