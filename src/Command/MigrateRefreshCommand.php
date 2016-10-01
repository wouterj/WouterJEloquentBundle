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

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class MigrateRefreshCommand extends BaseMigrateCommand
{
    protected function configure()
    {
        $this->setName('eloquent:migrate:refresh')
            ->setDescription('Reset and re-run all migrations')
            ->setHelp(<<<EOH
The <info>%command.name%</info> rolls back all migrations.

    <info>php %command.full_name%</info>
EOH
            )
            ->setDefinition([
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to use.'),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run in production.'),
                new InputOption('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The path of migrations files to be executed'),
                new InputOption('seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'),
                new InputOption('seeder', null, InputOption::VALUE_REQUIRED, 'The class name of the root seeder.'),
            ])
        ;
    }

    protected function execute(InputInterface $i, OutputInterface $o)
    {
        $force = $i->getOption('force');
        if (!$force && !$this->askConfirmationInProd($i, $o)) {
            return;
        }

        $database = $i->getOption('database');

        $this->call($o, 'eloquent:migrate:reset', [
            '--database' => $database,
            '--force'    => $force,
        ]);

        $this->call($o, 'eloquent:migrate', [
            '--database' => $database,
            '--force'    => $force,
            '--path'     => $i->getOption('path'),
        ]);

        if ($i->getOption('seed') || $i->getOption('seeder')) {
            $this->call($o, 'eloquent:seed', [
                '--database' => $database,
                '--class'    => $i->getOption('seeder') ?: 'DatabaseSeeder',
                '--force'    => $force,
            ]);
        }
    }

    private function call(OutputInterface $o, $name, array $arguments)
    {
        $command = $this->getApplication()->find($name);
        $command->run(new ArrayInput($arguments), $o);
    }
}
