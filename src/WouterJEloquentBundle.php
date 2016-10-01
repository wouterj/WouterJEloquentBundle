<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class WouterJEloquentBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DependencyInjection\WouterJEloquentExtension();
    }

    public function boot()
    {
        if ($this->container->has('wouterj_eloquent.initializer')) {
            $this->container->get('wouterj_eloquent.initializer')->initialize();
        }

        if ($this->container->has('wouterj_eloquent.facade.initializer')) {
            $this->container->get('wouterj_eloquent.facade.initializer')->initialize();
        }
    }
}
