<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateMakeCommand extends BaseMigrateCommand
{
    public function configure()
    {
        $this->setName('eloquent:migrate:make')
            ->setDescription('Creates a new migration file')
            ->setHelp(<<<EOT
The <info>%command.name%</info> creates a new migration file.

    <info>php %command.full_name%</info>
EOT
            )
            ->setDefinition(array(
                new InputArgument('name', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'The name of the migration'),
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to seed'),
                new InputOption('table', null, InputOption::VALUE_REQUIRED, 'An optional table name that is updated during the migration'),
                new InputOption('create', null, InputOption::VALUE_OPTIONAL, 'An optional table name that is created during the migration'),
            ))
        ;
    }

    public function execute(InputInterface $i, OutputInterface $o)
    {
        $o->writeln([
            'Creating a Migration',
            '====================',
        ]);

        $name = $i->getArgument('name');
        $table = $i->getOption('table');
        $create = $i->getOption('create');

        if (!$table && is_string($create)) {
            $table = $create;
        }

        $file = $this->writeMigrations($name, $table, (bool) $create);

        $o->writeln(sprintf('Migration `%s` is created!', $file));
    }

    private function writeMigrations($name, $table, $create)
    {
        return pathinfo(
            $this->getCreator()->create($name, $this->getMigrationPath(), $table, $create),
            PATHINFO_FILENAME
        );
    }

    private function getCreator()
    {
        return $this->getContainer()->get('wouterj_eloquent.migrations.creator');
    }
}
