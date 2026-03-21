<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\Events\ServiceContainerDispatcher;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias(Manager::class, 'wouterj_eloquent');
    $services->alias(DatabaseManager::class, 'wouterj_eloquent.database_manager');

    $services->set('wouterj_eloquent', Manager::class)
        ->public()
        ->call('setEventDispatcher', [service('wouterj_eloquent.events')]);

    $services->set('wouterj_eloquent.database_manager', DatabaseManager::class)
        ->public()
        ->factory([service('wouterj_eloquent'), 'getDatabaseManager']);

    $services->set('wouterj_eloquent.events', ServiceContainerDispatcher::class)
        ->args([null]); // wouterj_eloquent.observer locator
};
