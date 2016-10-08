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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WouterJ\EloquentBundle\Migrations\Migrator;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
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
