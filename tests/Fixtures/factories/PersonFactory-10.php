<?php

namespace App\Factory;

use WouterJ\EloquentBundle\Factory\Factory;

/**
 * @extends \WouterJ\EloquentBundle\Factory\Factory<\App\Model\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
