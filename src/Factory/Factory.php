<?php

namespace WouterJ\EloquentBundle\Factory;

use Illuminate\Database\Eloquent\Factories\Factory as IlluminateFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 * @extends IlluminateFactory<TModel>
 */
abstract class Factory extends IlluminateFactory
{
    public function modelName(): string
    {
        $resolver = static::$modelNameResolver ?: function (self $factory) {
            $name = $factory::class;
            if (str_ends_with($name, 'Factory')) {
                $name = substr($name, 0, -7);
            }

            return str_replace('\\Factory\\', '\\Model\\', $name);
        };

        return $this->model ?? $resolver($this);
    }
}
