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
use Symfony\Component\Console\Helper\Table;

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
                new InputOption('path', null, InputOption::VALUE_REQUIRED, 'The location where the migration file should be created'),
            ))
        ;
    }

    public function execute(InputInterface $i, OutputInterface $o): int
    {
        $migrator = $this->getMigrator();
        $migrator->setConnection($i->getOption('database'));

        if (!$migrator->repositoryExists()) {
            $o->writeln('<error>No migrations found.</>');

            return 1;
        }

        $ran = $migrator->getRepository()->getRan();
        $migrations = array_map([$migrator, 'getMigrationName'], $migrator->getMigrationFiles($this->getMigrationPaths($i)));

        $migrations = array_map(function ($migration) use ($ran, $migrator) {
            return in_array($migration, $ran)
                ? ['<info>Y</>', $migration]
                : ['<fg=red>N</>', $migration];
        }, $migrations);

        $table = (new Table($o))->setStyle('borderless');
        $table->setHeaders(['Ran?', 'Migration'])
            ->setRows($migrations);

        $table->render();

        return 0;
    }
}
