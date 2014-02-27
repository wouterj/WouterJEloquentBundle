<?php

$container->loadFromExtension('wouterj_eloquent', array(
    'connections' => array(
        'default' => array(
            'database' => 'database',
        ),
        'foo' => array(
            'driver' => 'sqlite',
            'host' => 'local',
            'database' => 'foo.db',
            'username' => 'user',
            'password' => 'pass',
            'prefix' => 'symfo_',
        ),
    ),
    'default_connection' => 'foo',
));
