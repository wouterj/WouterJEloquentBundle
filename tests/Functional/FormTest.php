<?php

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\CastingUser;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use WouterJ\EloquentBundle\Facade\Db;
use WouterJ\EloquentBundle\Facade\Schema;

class FormTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    protected function setUp()
    {
        static::bootKernel();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('password');
                $table->date('date_of_birth');
                $table->boolean('is_admin');
                $table->timestamps();
            });
        }
    }

    public function testFormTypeGuessing()
    {
        $client = static::createClient();

        $formView = $client->request('GET', '/user/create');
        $inputs = [];
        $formView->filterXPath('.//input')->each(function (Crawler $node) use (&$inputs) {
            $inputs[trim(str_replace('form_', '', $node->attr('id')), '[]')] = $node->attr('type');
        });
        $formView->filterXPath('.//select')->each(function (Crawler $node) use (&$inputs) {
            $inputs[trim(str_replace('form_', '', $node->attr('id')), '[]')] = 'select';
        });

        $this->assertEquals([
            'name' => 'text',
            'password' => 'text',
            'date_of_birth_year' => 'select',
            'date_of_birth_month' => 'select',
            'date_of_birth_day' => 'select',
            'is_admin' => 'checkbox',
        ], $inputs);
    }

    public function testFormSubmission()
    {
        $client = static::createClient();

        $formView = $client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form([
            'form[name]' => 'John Doe',
            'form[password]' => 's3cr3t',
            'form[date_of_birth][year]' => '2017',
            'form[date_of_birth][month]' => '10',
            'form[date_of_birth][day]' => '20',
            'form[is_admin]' => false,
        ]);
        $client->submit($form);

        $user = CastingUser::where(['name' => 'John Doe'])->first();
        $this->assertNotNull($user);
        $this->assertArraySubset([
            'name' => 'John Doe',
            'password' => 's3cr3t',
            'date_of_birth' => '2017-10-20 00:00:00',
            'is_admin' => '0',
        ], $user->getAttributes());
    }

    public function testFormValidation()
    {
        $client = static::createClient();

        $formView = $client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form([
            'form[name]' => '',
            'form[password]' => 's3cr3t',
            'form[date_of_birth][year]' => '2017',
            'form[date_of_birth][month]' => '10',
            'form[date_of_birth][day]' => '20',
            'form[is_admin]' => false,
        ]);
        $crawler = $client->submit($form);

        $this->assertCount(1, $crawler->filterXPath('//li[text()="The username should not be blank."]'));
    }
}
