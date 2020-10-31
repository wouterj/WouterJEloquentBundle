<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2017 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Command;

use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WouterJ\EloquentBundle\Migrations\Migrator;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateFreshCommand extends BaseMigrateCommand
{
    private $db;

    public function __construct(DatabaseManager $db, Migrator $migrator, string $migrationPath, string $kernelEnv)
    {
        parent::__construct($migrator, $migrationPath, $kernelEnv);

        $this->db = $db;
    }

    protected function configure(): void
    {
        $this->setName('eloquent:migrate:fresh')
            ->setDescription('Drop all tables and re-run all migrations.')
            ->setDefinition([
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to use.'),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run in production.'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The path of migrations files to be executed'),
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
        $this->dropAllTables($database);

        $output->writeln('Dropped all tables successfully.');

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

    private function dropAllTables($database): void
    {
        $this->db->connection($database)
            ->getSchemaBuilder()
            ->dropAllTables();
    }
}
