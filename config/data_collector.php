<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\DataCollector\EloquentDataCollector;
use WouterJ\EloquentBundle\DataCollector\QueryListener;
use WouterJ\EloquentBundle\Twig\SqlFormatterExtension;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('wouterj_eloquent.data_collector', EloquentDataCollector::class)
        ->args([service('wouterj_eloquent'), service('wouterj_eloquent.query_listener')])
        ->tag('data_collector', [
            'id' => 'wouterj_eloquent.eloquent_collector',
            'template' => '@WouterJEloquent/data_collector/eloquent.html.twig',
        ]);

    $services->set('wouterj_eloquent.query_listener', QueryListener::class);

    $services->set('wouterj_eloquent.twig.sql_format_extension', SqlFormatterExtension::class)
        ->tag('twig.extension');
};
