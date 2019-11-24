<?php

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\CastingUser;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use WouterJ\EloquentBundle\Facade\Db;
use WouterJ\EloquentBundle\Facade\Schema;

class FormTest extends WebTestCase
{
    use SetUpTearDownTrait;

    /** @var KernelBrowser */
    private $client;

    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    protected function doSetUp()
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
        $formView = $this->client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form([
            'form[name]' => 'John Doe',
            'form[password]' => 's3cr3t',
            'form[date_of_birth][year]' => '2017',
            'form[date_of_birth][month]' => '10',
            'form[date_of_birth][day]' => '20',
            'form[is_admin]' => false,
        ]);
        $this->client->submit($form);

        $user = CastingUser::where(['name' => 'John Doe'])->first();
        $this->assertNotNull($user);

        $userAttr = $user->getAttributes();
        $this->assertEquals('John Doe', $userAttr['name']);
        $this->assertEquals('s3cr3t', $userAttr['password']);
        $this->assertEquals('2017-10-20 00:00:00', $userAttr['date_of_birth']);
        $this->assertEquals('0', $userAttr['is_admin']);
    }

    public function testFormValidation()
    {
        $formView = $this->client->request('GET', '/user/create');
        $form = $formView->selectButton('Submit')->form([
            'form[name]' => '',
            'form[password]' => 's3cr3t',
            'form[date_of_birth][year]' => '2017',
            'form[date_of_birth][month]' => '10',
            'form[date_of_birth][day]' => '20',
            'form[is_admin]' => false,
        ]);
        $crawler = $this->client->submit($form);

        $this->assertCount(1, $crawler->filterXPath('//li[text()="The username should not be blank."]'));
    }
}
