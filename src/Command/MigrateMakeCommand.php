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

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WouterJ\EloquentBundle\Migrations\Creator;
use WouterJ\EloquentBundle\Migrations\Migrator;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateMakeCommand extends BaseMigrateCommand
{
    private $creator;

    public function __construct(Creator $creator, Migrator $migrator, string $migrationPath, string $kernelEnv)
    {
        parent::__construct($migrator, $migrationPath, $kernelEnv);

        $this->creator = $creator;
    }

    public function configure(): void
    {
        $this->setName('eloquent:migrate:make')
            ->setDescription('Creates a new migration file')
            ->setHelp(<<<EOT
The <info>%command.name%</info> creates a new migration file.

    <info>php %command.full_name%</info>
EOT
            )
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'The name of the migration'),
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to seed'),
                new InputOption('table', null, InputOption::VALUE_REQUIRED, 'An optional table name that is updated during the migration'),
                new InputOption('create', null, InputOption::VALUE_OPTIONAL, 'An optional table name that is created during the migration'),
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The location where the migration file should be created'),
            ))
        ;
    }

    public function execute(InputInterface $i, OutputInterface $o): int
    {
        $o->writeln([
            'Creating a Migration',
            '====================',
        ]);

        $name = $i->getArgument('name');
        $table = $i->getOption('table');
        $create = $i->getOption('create');
        $paths = $this->getMigrationPaths($i);

        if (!$table && is_string($create)) {
            $table = $create;
        }

        // guess table name based on the name
        if (!$table) {
            if (preg_match('/^create_(\w+)_table$/', $name, $matches)) {
                $table = $matches[1];

                $create = true;
            }
        }

        $file = $this->writeMigrations($name, array_shift($paths), $table, (bool) $create);

        $o->writeln(sprintf('Migration `%s` is created!', $file));

        return 0;
    }

    private function writeMigrations($name, $path, $table, $create): string
    {
        return pathinfo(
            $this->creator->create($name, $path, $table, $create),
            PATHINFO_FILENAME
        );
    }
}
