<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Command;

use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class TestCommand
{
    private $command;
    /** @var CommandTester */
    private $tester;
    private $inputStream = "";
    private $options = [];
    private $arguments = [];

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public static function create($command)
    {
        return new static($command);
    }

    public function answering($answer)
    {
        $this->inputStream .= $answer."\n";

        return $this;
    }

    public function passing($name, $value = true)
    {
        if ('-' !== $name[0]) {
            $this->arguments[] = $value;
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function execute()
    {
        $this->tester = new CommandTester($this->command);

        if (!$this->command->getHelperSet()) {
            $this->command->setHelperSet(new HelperSet([new QuestionHelper()]));
        }

        if ('' !== $this->inputStream) {
            if (method_exists($this->tester, 'setInputs')) {
                $this->tester->setInputs(explode($this->inputStream, "\n"));
            } else {
                // todo Remove if Symfony <3.2 support is dropped
                $this->command->getHelper('question')
                    ->setInputStream($this->getInputStream($this->inputStream));
            }
        }

        $this->tester->execute(array_merge($this->options, $this->arguments), ['decorated' => false]);

        return $this;
    }

    public function duringExecute()
    {
        return $this->execute();
    }

    public function outputs($expected)
    {
        Assert::assertStringContainsString($expected, $this->tester->getDisplay(true));

        return $this;
    }

    public function doesNotOutput($notExpected)
    {
        Assert::assertStringNotContainsString($notExpected, $this->tester->getDisplay(true));

        return $this;
    }

    public function exitsWith($code)
    {
        Assert::assertSame($code, $this->tester->getStatusCode());

        return $this;
    }

    private function getInputStream($str)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $str);
        rewind($stream);

        return $stream;
    }
}
