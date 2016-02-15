<?php

namespace WouterJ\EloquentBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends BaseMigrateCommand
{
    public function configure()
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
            ])
        ;
    }

    public function execute(InputInterface $i, OutputInterface $o)
    {
        if (!$i->getOption('force') && !$this->askConfirmationInProd($i, $o)) {
            return;
        }

        if (null !== $path = $i->getOption('path')) {
            $path = getcwd().'/'.$path;
        } else {
            $path = $this->getMigrationPath();
        }

        $this->getMigrator()->run($path, [
            'pretend' => $i->getOption('pretend'),
        ]);

        foreach ($this->getMigrator()->getNotes() as $note) {
            $o->writeln($note);
        }

        if ($i->getOption('seed')) {
            $command = $this->getApplication()->find('eloquent:seed');
            $command->run(new ArrayInput(['--force' => true]), $o);
        }
    }
}
