<?php

$container->loadFromExtension('wouterj_eloquent', [
    'database' => 'db',
    'aliases' => ['schema' => true],
]);
