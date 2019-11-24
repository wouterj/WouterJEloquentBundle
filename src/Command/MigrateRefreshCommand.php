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
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateRefreshCommand extends BaseMigrateCommand
{
    protected function configure(): void
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
                new InputOption('step', null, InputOption::VALUE_REQUIRED, 'The number of migrations to be reverted'),
                new InputOption('seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'),
                new InputOption('seeder', null, InputOption::VALUE_REQUIRED, 'The class name of the root seeder.'),
            ])
        ;
    }

    protected function execute(InputInterface $i, OutputInterface $o): int
    {
        $force = $i->getOption('force');
        if (!$force && !$this->askConfirmationInProd($i, $o)) {
            return 1;
        }

        $database = $i->getOption('database');
        $step = (int) $i->getOption('step');

        if ($step > 0) {
            $this->call($o, 'eloquent:migrate:rollback', [
                '--database' => $database,
                '--force'    => $force,
                '--path'     => $i->getOption('path'),
                '--step'     => $step,
            ]);
        } else {
            $this->call($o, 'eloquent:migrate:reset', [
                '--database' => $database,
                '--force'    => $force,
                '--path'     => $i->getOption('path'),
            ]);
        }

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

        return 0;
    }
}
