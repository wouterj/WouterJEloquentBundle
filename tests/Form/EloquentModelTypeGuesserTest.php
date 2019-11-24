<?php

namespace WouterJ\EloquentBundle\Form;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

class EloquentModelTypeGuesserTest extends TestCase
{
    use SetUpTearDownTrait;

    protected $subject;

    protected function doSetUp()
    {
        $this->subject = new EloquentModelTypeGuesser();
    }

    /** @test */
    public function it_ignores_non_eloquent_models()
    {
        $this->assertNull($this->subject->guessType(NoModel::class, 'title'));
    }
}

class NoModel
{
    public $title;
}
