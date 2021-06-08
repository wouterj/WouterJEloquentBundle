<?php

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\User;
use Illuminate\Database\Schema\Blueprint;
use WouterJ\EloquentBundle\Facade\Schema;

class SecurityTest extends AbstractFunctionalTest
{
    protected function setUp(): void
    {
        $this->client = static::createClient();

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

    private function createUser()
    {
        $user = new User();
        $user->name = 'Wouter';
        $user->email = 'wouter@example.com';
        $user->password = 'L@r@v3L';
        $user->save();
    }

    public function testLogin()
    {
        $this->createUser();

        $this->client->request('GET', '/secured/profile', [], [], [
            'PHP_AUTH_USER' => 'wouter@example.com',
            'PHP_AUTH_PW' => 'L@r@v3L',
        ]);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertEquals('Name: Wouter', $response->getContent());
    }

    public function testFailedLogin()
    {
        $this->client->request('GET', '/secured/profile', [], [], [
            'PHP_AUTH_USER' => 'wouter@not-existent.com',
            'PHP_AUTH_PW' => 'L@r@v3L',
        ]);
        $response = $this->client->getResponse();

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testRefreshingUser()
    {
        $this->createUser();

        $this->client->request('GET', '/secured/profile', [], [], [
            'PHP_AUTH_USER' => 'wouter@example.com',
            'PHP_AUTH_PW' => 'L@r@v3L',
        ]);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk(), 'Not authenticated using HTTP basic.');

        // modify the user, so we can be sure the user is refreshed
        $user = User::where('email', 'wouter@example.com')->first();
        $user->name = 'Modified';
        $user->save();

        // use the session
        $this->client->request('GET', '/secured/profile');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk(), 'Not authenticated using the session.');
        $this->assertEquals('Name: Modified', $response->getContent());
    }
}
