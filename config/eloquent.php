<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\EventListener\EloquentInitializer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('wouterj_eloquent.initializer', EloquentInitializer::class)
        ->public()
        ->args([service('wouterj_eloquent'), param('wouterj_eloquent.default_connection')]);
};
