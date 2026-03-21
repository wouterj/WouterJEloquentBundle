<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\EventListener\FacadeInitializer;
use WouterJ\EloquentBundle\Facade\AliasesLoader;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('wouterj_eloquent.facade.initializer', FacadeInitializer::class)
        ->public()
        ->args([service('service_container')]);

    $services->set('wouterj_eloquent.aliases.loader', AliasesLoader::class);
};
