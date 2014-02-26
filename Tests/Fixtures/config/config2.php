<?php

$container->loadFromExtension('wj_eloquent', array(
    'database' => 'database',
    'aliases' => array(
        'schema' => true,
    ),
));
