<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\Maker\MakeFactory;
use WouterJ\EloquentBundle\Maker\MakeMigration;
use WouterJ\EloquentBundle\Maker\MakeModel;
use WouterJ\EloquentBundle\Maker\MakeSeeder;
use WouterJ\EloquentBundle\Migrations\Creator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('wouterj_eloquent.migrations.creator', Creator::class)
        ->args([service('maker.file_manager')]);

    $services->set('wouterj_eloquent.maker.seeder', MakeSeeder::class)
        ->args([service('maker.file_manager')])
        ->tag('maker.command');

    $services->set('wouterj_eloquent.maker.model', MakeModel::class)
        ->tag('maker.command');

    $services->set('wouterj_eloquent.maker.factory', MakeFactory::class)
        ->args([service('maker.file_manager')])
        ->tag('maker.command');

    $services->set('wouterj_eloquent.maker.migration', MakeMigration::class)
        ->args([service('wouterj_eloquent.migrations.creator'), param('wouterj_eloquent.migration_path')])
        ->tag('maker.command');
};
