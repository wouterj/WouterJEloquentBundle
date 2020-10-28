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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');
        if (!$force && !$this->askConfirmationInProd($input, $output)) {
            return 1;
        }

        $database = $input->getOption('database');
        $step = (int) $input->getOption('step');

        if ($step > 0) {
            $this->call($output, 'eloquent:migrate:rollback', [
                '--database' => $database,
                '--force'    => $force,
                '--path'     => $input->getOption('path'),
                '--step'     => $step,
            ]);
        } else {
            $this->call($output, 'eloquent:migrate:reset', [
                '--database' => $database,
                '--force'    => $force,
                '--path'     => $input->getOption('path'),
            ]);
        }

        $this->call($output, 'eloquent:migrate', [
            '--database' => $database,
            '--force'    => $force,
            '--path'     => $input->getOption('path'),
        ]);

        if ($input->getOption('seed') || $input->getOption('seeder')) {
            $this->call($output, 'eloquent:seed', [
                'class'      => [$input->getOption('seeder') ?: 'DatabaseSeeder'],
                '--database' => $database,
                '--force'    => $force,
            ]);
        }

        return 0;
    }
}
