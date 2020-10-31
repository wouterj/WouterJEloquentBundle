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

use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MigrateInstallCommand extends Command
{
    private $migrationRepository;

    public function __construct(MigrationRepositoryInterface $migrationRepository)
    {
        parent::__construct();

        $this->migrationRepository = $migrationRepository;
    }

    protected function configure(): void
    {
        $this->setName('eloquent:migrate:install')
            ->setDescription('Creates the migration repository.')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->migrationRepository;
        $repository->setSource($input->getOption('database'));
        $repository->createRepository();

        $output->writeln('<comment>Migration table created successfully.</>');

        return 0;
    }
}
