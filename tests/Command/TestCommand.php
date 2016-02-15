<?php

namespace WouterJ\EloquentBundle\Command;

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
        if ('-' !== $name[1]) {
            $this->arguments[] = $value;
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function execute()
    {
        $this->tester = new CommandTester($this->command);

        if ('' !== $this->inputStream) {
            if ($this->command->getHelperSet()) {
                $helper = $this->command->getHelper('question');
            } else {
                $this->command->setHelperSet(new HelperSet([$helper = new QuestionHelper()]));
            }

            $helper->setInputStream($this->getInputStream($this->inputStream));
        }

        $this->tester->execute(array_merge($this->options, $this->arguments));

        return $this;
    }

    public function duringExecute()
    {
        return $this->execute();
    }

    public function outputs($expected)
    {
        \PHPUnit_Framework_Assert::assertContains($expected, $this->tester->getDisplay(true));

        return $this;
    }

    public function doesNotOutput($notExpected)
    {
        \PHPUnit_Framework_Assert::assertNotContains($notExpected, $this->tester->getDisplay(true));

        return $this;
    }

    public function exitsWith($code)
    {
        \PHPUnit_Framework_Assert::assertSame($code, $this->tester->getStatusCode());

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
