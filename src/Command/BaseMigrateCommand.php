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
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class BaseMigrateCommand extends ContainerAwareCommand
{
    protected function getMigrationPath()
    {
        return $this->getContainer()->getParameter('wouterj_eloquent.migration_path');
    }

    protected function getMigrationPaths(InputInterface $input = null)
    {
        if (null !== $input && $input->hasOption('path') && null !== $path = $input->getOption('path')) {
            return [getcwd().'/'.$path];
        }

        return array_merge([$this->getMigrationPath()], $this->getMigrator()->paths());
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

    protected function call(OutputInterface $o, $name, array $arguments)
    {
        $command = $this->getApplication()->find($name);
        $command->run(new ArrayInput($arguments), $o);
    }
}
