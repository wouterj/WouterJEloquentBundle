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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateResetCommand extends BaseMigrateCommand
{
    protected function configure()
    {
        $this->setName('eloquent:migrate:reset')
            ->setDescription('Rollback all database migrations')
            ->setHelp(<<<EOH
The <info>%command.name%</info> rolls back all migrations.

    <info>php %command.full_name%</info>
EOH
            )
            ->setDefinition([
                new InputOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run in production.'),
                new InputOption('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The path of migrations files to be executed'),
            ])
        ;
    }

    protected function execute(InputInterface $i, OutputInterface $o)
    {
        if (!$i->getOption('force') && !$this->askConfirmationInProd($i, $o)) {
            return;
        }

        $migrator = $this->getMigrator();
        $migrator->setConnection($i->getOption('database'));

        if (!$migrator->repositoryExists()) {
            $o->writeln('<error>Migration table not found.</>');

            return 1;
        }

        $migrator->reset($this->getMigrationPaths($i), $i->getOption('pretend'));

        foreach ($migrator->getNotes() as $note) {
            $o->writeln($note);
        }
    }
}
