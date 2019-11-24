<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\User;
use AppBundle\Model\SoftDeleteUser;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WouterJ\EloquentBundle\Facade\Schema;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
abstract class EventsTest extends KernelTestCase
{
    use SetUpTearDownTrait;

    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    protected function reset() { }
    abstract protected function getLogs();

    protected function doSetUp()
    {
        static::bootKernel();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email');
                $table->string('password');
                $table->timestamps();
            });
        }

        $this->reset();
    }

    public function testCreationEvents()
    {
        User::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $this->assertEquals([
            'saving John Doe',
            'creating John Doe',
            'created John Doe',
            'saved John Doe',
        ], $this->getLogs());
    }

    public function testUpdateEvents()
    {
        User::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $this->reset();

        $user = User::where('name', 'John Doe')->first();
        $user->name = 'Ben Doe';

        $user->save();

        $this->assertEquals([
            'saving Ben Doe',
            'updating Ben Doe',
            'updated Ben Doe',
            'saved Ben Doe',
        ], $this->getLogs());
    }

    public function testDeletionEvents()
    {
        User::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $this->reset();

        $user = User::where('name', 'John Doe')->first();
        $user->delete();

        $this->assertEquals([
            'deleting John Doe',
            'deleted John Doe',
        ], $this->getLogs());
    }

    public function testRestoreEvents()
    {
        Schema::create('soft_delete_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });

        SoftDeleteUser::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $user = SoftDeleteUser::where('name', 'John Doe')->first();
        $user->delete();

        $this->reset();

        $user = SoftDeleteUser::withTrashed()->where('name', 'John Doe')->first();
        $user->restore();

        $this->assertEquals([
            'restoring John Doe',
            'saving John Doe',
            'updating John Doe',
            'updated John Doe',
            'saved John Doe',
            'restored John Doe',
        ], $this->getLogs());
    }

    public function testListeners()
    {
        $created = false;
        User::created(function () use (&$created) {
            $created = true;
        });

        User::create([
            'name'     => 'John Doe',
            'email'    => 'j.doe@example.com',
            'password' => 'pa$$word',
        ]);

        $this->assertTrue($created);
    }
}
