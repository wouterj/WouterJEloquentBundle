<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\EventListener;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Initializes the Eloquent ORM.
 *
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class EloquentInitializer
{
    /** @var Capsule */
    private $capsule;
    private $defaultConnection;

    public function __construct(Capsule $capsule, string $defaultConnection = 'default')
    {
        $this->capsule = $capsule;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * Initializes the Eloquent ORM.
     */
    public function initialize(): void
    {
        $this->capsule->bootEloquent();

        if ('default' !== $this->defaultConnection) {
            $this->capsule->getDatabaseManager()->setDefaultConnection($this->defaultConnection);
        }
    }
}
