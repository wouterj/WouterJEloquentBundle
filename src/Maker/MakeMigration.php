<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Console\Migrations\TableGuesser;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use WouterJ\EloquentBundle\Migrations\Creator;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MakeMigration extends AbstractMaker
{
    private $creator;
    private $migrationPath;

    public function __construct(Creator $creator, string $migrationPath)
    {
        $this->creator = $creator;
        $this->migrationPath = $migrationPath;
    }

    public static function getCommandName(): string
    {
        return 'make:eloquent-migration';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new Eloquent migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration')
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to seed')
            ->addOption('table', null, InputOption::VALUE_REQUIRED, 'An optional table name that is updated during the migration')
            ->addOption('create', null, InputOption::VALUE_OPTIONAL, 'An optional table name that is created during the migration', false)
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = Str::asSnakeCase($input->getArgument('name'));
        $table = $input->getOption('table');
        $create = $input->getOption('create');

        if (!$table && is_string($create)) {
            $table = $create;
        }

        // guess table name based on the name
        if (!$table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        $this->creator->create($name, $this->migrationPath, $table, (bool) $create);
    }
}
