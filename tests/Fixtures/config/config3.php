<?php

$container->loadFromExtension('wouterj_eloquent', [
    'driver' => 'sqlite',
    'host' => 'local',
    'database' => 'foo.db',
    'username' => 'user',
    'password' => 'pass',
    'prefix' => 'symfo_',
    'eloquent' => true,
    'aliases' => true,
]);
