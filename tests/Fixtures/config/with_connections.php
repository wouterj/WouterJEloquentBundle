<?php

$container->loadFromExtension('wouterj_eloquent', [
    'connections' => [
        'default' => ['database' => 'db'],
        'connection_1' => [
            'driver' => 'sqlite',
            'host' => 'local',
            'port' => null,
            'database' => 'foo.db',
            'username' => 'user',
            'password' => 'pass',
            'prefix' => 'symfo_',
            'schema' => 'schema1'
        ],
    ],
    'default_connection' => 'connection_1',
]);
