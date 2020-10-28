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

use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateCommand extends BaseMigrateCommand
{
    public function configure(): void
    {
        $this->setName('eloquent:migrate')
            ->setDescription('Executes a migration.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> executes a migration.

    <info>php %command.full_name%</info>
EOT
            )
            ->setDefinition([
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to use'),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The path of migrations files to be executed'),
                new InputOption('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run'),
                new InputOption('seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'),
                new InputOption('step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually'),
            ])
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force') && !$this->askConfirmationInProd($input, $output)) {
            return 1;
        }

        $this->getMigrator()->setOutput(new OutputStyle($input, $output));

        $this->getMigrator()->run($this->getMigrationPaths($input), [
            'pretend' => $input->getOption('pretend'),
            'step'    => $input->getOption('step'),
        ]);

        if ($input->getOption('seed')) {
            $this->call($output, 'eloquent:seed', ['--force' => true]);
        }

        return 0;
    }
}
