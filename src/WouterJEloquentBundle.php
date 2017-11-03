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

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WouterJ\EloquentBundle\DependencyInjection\Compiler\AddCasterPass;
use WouterJ\EloquentBundle\DependencyInjection\Compiler\ObserverPass;
use WouterJ\EloquentBundle\Command;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @final
 * @internal
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
        $container->addCompilerPass(new AddCasterPass());
    }

    public function registerCommands(Application $application)
    {
        $application->add(new Command\MigrateCommand);
        $application->add(new Command\MigrateInstallCommand);
        $application->add(new Command\MigrateMakeCommand);
        $application->add(new Command\MigrateRefreshCommand);
        $application->add(new Command\MigrateResetCommand);
        $application->add(new Command\MigrateRollbackCommand);
        $application->add(new Command\MigrateStatusCommand);
        $application->add(new Command\SeedCommand);
    }

    public function boot()
    {
        if ($this->container->has('wouterj_eloquent.initializer')) {
            $this->container->get('wouterj_eloquent.initializer')->initialize();
        }

        if ($this->container->has('wouterj_eloquent.facade.initializer')) {
            $this->container->get('wouterj_eloquent.facade.initializer')->initialize();
        }
        
        //fixes compatibility symfony v2 issues with misinterpretation of the annotation @mixin within Eloquent's model
        AnnotationReader::addGlobalIgnoredName('mixin');
    }

    public function getPath()
    {
        return dirname(__DIR__);
    }
}
