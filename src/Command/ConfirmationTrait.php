<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @method mixed getHelper(string $name)
 */
trait ConfirmationTrait
{
    private $kernelEnv;

    protected function askConfirmationInProd(InputInterface $i, OutputInterface $o): bool
    {
        if ('prod' !== $this->kernelEnv) {
            return true;
        }

        return $this->getHelper('question')
            ->ask($i, $o, new ConfirmationQuestion('Are you sure you want to execute the migrations in production?', false));
    }
}
