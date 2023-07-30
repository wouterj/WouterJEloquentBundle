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

use Illuminate\Console\View\Components;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use WouterJ\EloquentBundle\Migrations\Migrator;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateStatusCommand extends BaseMigrateCommand
{
    public function configure(): void
    {
        $this->setName('eloquent:migrate:status')
            ->setDescription('Show the status of each migration')
            ->setHelp(<<<EOT
The <info>%command.name%</info> creates a new migration file.

    <info>php %command.full_name%</info>
EOT
            )
            ->setDefinition(array(
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to seed'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The location where the migration files are stored'),
            ))
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = $this->getMigrator();

        return $migrator->usingConnection($input->getOption('database'), function () use ($migrator, $input, $output): int {
            if (!$migrator->repositoryExists()) {
                $this->error($output, 'No migrations found.');

                return 1;
            }

            $migrations = $migrator->getMigrationFiles($this->getMigrationPaths($input));
            if ($migrations) {
                $this->writeStatus($output, $migrator, $migrations);
            } else {
                $this->info($output, 'No migrations found');
            }

            return 0;
        });
    }

    private function writeStatus(OutputInterface $output, Migrator $migrator, array $migrations): void
    {
        $ran = $migrator->getRepository()->getRan();
        if (class_exists(Components\TwoColumnDetail::class)) {
            $output->writeln('');

            (new Components\TwoColumnDetail($output))->render('<fg=gray>Migration name</>', '<fg=gray>Batch / Status</>');

            $batches = $migrator->getRepository()->getMigrationBatches();
            foreach ($migrations as $migration) {
                $migrationName = $migrator->getMigrationName($migration);
                $status = in_array($migrationName, $ran)
                    ? '<fg=green;options=bold>Ran</>'
                    : '<fg=yellow;options=bold>Pending</>';

                if (\in_array($migrationName, $ran)) {
                    $status = '['.$batches[$migrationName].'] '.$status;
                }

                (new Components\TwoColumnDetail($output))->render($migrationName, $status);
            }

            $output->writeln('');
        } else {
            // BC Laravel <9.39
            $migrations = array_map([$migrator, 'getMigrationName'], $migrations);

            $migrations = array_map(function ($migration) use ($ran, $migrator) {
                return in_array($migration, $ran)
                    ? ['<info>Y</>', $migration]
                    : ['<fg=red>N</>', $migration];
            }, $migrations);

            $table = (new Table($output))->setStyle('borderless');
            $table->setHeaders(['Ran?', 'Migration'])
                ->setRows($migrations);

            $table->render();
        }
    }
}
