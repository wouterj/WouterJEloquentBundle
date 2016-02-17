<?php

$container->loadFromExtension('wouterj_eloquent', [
    'database' => 'database',
    'aliases' => [
        'schema' => true,
    ],
]);
