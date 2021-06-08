<?php

namespace WouterJ\EloquentBundle\Form;

use PHPUnit\Framework\TestCase;

class EloquentModelTypeGuesserTest extends TestCase
{
    protected $subject;

    protected function setUp(): void
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
