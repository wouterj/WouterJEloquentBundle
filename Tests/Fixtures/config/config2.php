<?php

$container->loadFromExtension('wouterj_eloquent', array(
    'database' => 'database',
    'aliases' => array(
        'schema' => true,
    ),
));
