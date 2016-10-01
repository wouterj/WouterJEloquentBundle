<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WouterJ\EloquentBundle\Migrations\Migrator;

abstract class BaseMigrateCommand extends ContainerAwareCommand
{
    protected function getMigrationPath()
    {
        return $this->getContainer()->getParameter('wouterj_eloquent.migration_path');
    }

    protected function askConfirmationInProd(InputInterface $i, OutputInterface $o)
    {
        if ('prod' !== $this->getContainer()->getParameter('kernel.environment')) {
            return true;
        }

        return $this->getHelper('question')
            ->ask($i, $o, new ConfirmationQuestion('Are you sure you want to execute the migrations in production?', false));
    }

    /** @return Migrator */
    protected function getMigrator()
    {
        return $this->getContainer()->get('wouterj_eloquent.migrator');
    }
}
