<?php

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\CastingUser;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use WouterJ\EloquentBundle\Facade\Db;
use WouterJ\EloquentBundle\Facade\Schema;

class FormTest extends AbstractFunctionalTest
{
    protected function setUp(): void
    {
        $this->client = static::createClient();

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
        $formView = $this->client->request('GET', '/user/create');
        preg_match('/\<select.+?\>/', $this->client->getResponse()->getContent(), $m);
        $inputs = [];
        $formView->filterXPath('.//input')->each(function (Crawler $node) use (&$inputs) {
            $inputs[trim(str_replace('form_', '', $node->attr('id')), '[]')] = $node->attr('type');
        });
        $formView->filterXPath('.//select')->each(function (Crawler $node) use (&$inputs) {
            $inputs[trim(str_replace('form_', '', $node->attr('id')), '[]')] = 'select';
        });

        $expectedTypes = [
            'name' => 'text',
            'password' => 'text',
            'is_admin' => 'checkbox',
            'date_of_birth' => 'date',
        ];
        if (!\array_key_exists('date_of_birth', $inputs)) {
            $expectedTypes['date_of_birth_year'] = 'select';
            $expectedTypes['date_of_birth_month'] = 'select';
            $expectedTypes['date_of_birth_day'] = 'select';
            unset($expectedTypes['date_of_birth']);
        }

        $this->assertEquals($expectedTypes, $inputs);
    }

    public function testFormSubmission()
    {
        $birthDay = new \DateTimeImmutable('-3 years');

        $formView = $this->client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form();

        $form['form[name]'] = 'John Doe';
        $form['form[password]'] = 's3cr3t';
        if (!isset($form['form[date_of_birth][year]'])) {
            $form['form[date_of_birth]'] = $birthDay->format('Y-n-j');
        } else {
            // BC for Symfony <7
            $form['form[date_of_birth][year]'] = $birthDay->format('Y');
            $form['form[date_of_birth][month]'] = $birthDay->format('n');
            $form['form[date_of_birth][day]'] = $birthDay->format('j');
        }
        $form['form[is_admin]'] = false;

        $this->client->submit($form);

        $user = CastingUser::where(['name' => 'John Doe'])->first();
        $this->assertNotNull($user);

        $userAttr = $user->getAttributes();
        $this->assertEquals('John Doe', $userAttr['name']);
        $this->assertEquals('s3cr3t', $userAttr['password']);
        $this->assertEquals($birthDay->format('Y-m-d').' 00:00:00', $userAttr['date_of_birth']);
        $this->assertEquals('0', $userAttr['is_admin']);
    }

    public function testFormValidation()
    {
        $formView = $this->client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form();
        $form['form[name]'] = '';
        $form['form[password]'] = 's3cr3t';
        $form['form[is_admin]'] = false;
        if (!isset($form['form[date_of_birth][year]'])) {
            $form['form[date_of_birth]'] = (new \DateTimeImmutable())->format('Y').'-10-20';
        } else {
            // BC for Symfony <7
            $form['form[date_of_birth][year]'] = (new \DateTimeImmutable())->format('Y');
            $form['form[date_of_birth][month]'] = '10';
            $form['form[date_of_birth][day]'] = '20';
        }
        $crawler = $this->client->submit($form);

        $this->assertCount(1, $crawler->filterXPath('//li[text()="The username should not be blank."]'));
    }
}
