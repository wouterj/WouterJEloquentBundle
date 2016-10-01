<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Seed;

use WouterJ\EloquentBundle\Seeder;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);
    }
}
