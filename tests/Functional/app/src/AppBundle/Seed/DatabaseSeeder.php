<?php

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
