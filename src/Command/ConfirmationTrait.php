<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @method HelperInterface getHelper(string $name)
 */
trait ConfirmationTrait
{
    private $kernelEnv;

    protected function askConfirmationInProd(InputInterface $i, OutputInterface $o): bool
    {
        if ('prod' !== $this->kernelEnv) {
            return true;
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        return $helper->ask($i, $o, new ConfirmationQuestion('Are you sure you want to execute the migrations in production?', false));
    }
}
