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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WouterJ\EloquentBundle\Facade\Schema;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class EventsTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/TestKernel.php';

        return 'TestKernel';
    }

    protected function setUp()
    {
        parent::setUp();

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
    }

    /** @dataProvider getEventsData */
    public function testEvents($setup, $execute, array $expectedEvents)
    {
        ob_start();
        $setup();
        ob_clean();

        $execute();
        $this->assertEquals(implode("\n", $expectedEvents)."\n", ob_get_contents());

        ob_end_clean();
    }

    public function getEventsData()
    {
        return [
            'creation' => [
                function () {},
                function () {
                    User::create([
                        'name'     => 'John Doe',
                        'email'    => 'j.doe@example.com',
                        'password' => 'pa$$word',
                    ]);
                },
                [
                    'saving John Doe',
                    'creating John Doe',
                    'created John Doe',
                    'saved John Doe',
                ]
            ],
            'update' => [
                function () {
                    User::create([
                        'name'     => 'John Doe',
                        'email'    => 'j.doe@example.com',
                        'password' => 'pa$$word',
                    ]);
                },
                function () {
                    $user = User::where('name', 'John Doe')->first();
                    $user->name = 'Ben Doe';

                    $user->save();
                },
                [
                    'saving Ben Doe',
                    'updating Ben Doe',
                    'updated Ben Doe',
                    'saved Ben Doe',
                ]
            ],
            'deletion' => [
                function () {
                    User::create([
                        'name'     => 'John Doe',
                        'email'    => 'j.doe@example.com',
                        'password' => 'pa$$word',
                    ]);
                },
                function () {
                    $user = User::where('name', 'John Doe')->first();
                    $user->delete();
                },
                [
                    'deleting John Doe',
                    'deleted John Doe',
                ]
            ],
            'restore' => [
                function () {
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
                },
                function () {
                    $user = SoftDeleteUser::withTrashed()->where('name', 'John Doe')->first();
                    $user->restore();
                },
                [
                    'restoring John Doe',
                    'saving John Doe',
                    'updating John Doe',
                    'updated John Doe',
                    'saved John Doe',
                    'restored John Doe',
                ]
            ],
        ];
    }
}
