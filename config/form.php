<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WouterJ\EloquentBundle\Form\EloquentModelTypeGuesser;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('wouterj_eloquent.form.type_guesser', EloquentModelTypeGuesser::class)
        ->tag('form.type_guesser');
};
