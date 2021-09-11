<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\VarDumper;

use Symfony\Component\VarDumper\Cloner\Stub;
use Illuminate\Database\Eloquent\Model;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@amber.team>
 */
class EloquentCaster
{
    public static function castModel(Model $model, array $data, Stub $stub, bool $isNested): array
    {
        return $model->attributesToArray();
    }
}
