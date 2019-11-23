<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\User;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WouterJ\EloquentBundle\Facade\Schema;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class EloquentTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    public function testRunningMigrations()
    {
        static::bootKernel();

        // create user table
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });

        $created = User::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $user = User::find($created->id);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('j.doe@example.com', $user->email);
        $this->assertEquals('pa$$word', $user->password);
    }
}

