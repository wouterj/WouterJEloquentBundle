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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WouterJ\EloquentBundle\Migrations\Migrator;

/**
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
abstract class BaseMigrateCommand extends Command
{
    use ConfirmationTrait;

    /** @var Migrator */
    private $migrator;
    private $migrationPath;
    private $kernelEnv;

    public function __construct(Migrator $migrator, string $migrationPath, string $kernelEnv)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->migrationPath = $migrationPath;
        $this->kernelEnv = $kernelEnv;
    }

    protected function getMigrationPath(): string
    {
        return $this->migrationPath;
    }

    protected function getMigrationPaths(InputInterface $input = null): array
    {
        if (null !== $input && $input->hasOption('path') && null !== $path = $input->getOption('path')) {
            return [getcwd().'/'.$path];
        }

        return array_merge([$this->getMigrationPath()], $this->getMigrator()->paths());
    }

    protected function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    protected function call(OutputInterface $o, $name, array $arguments): void
    {
        $command = $this->getApplication()->find($name);
        $command->run(new ArrayInput($arguments), $o);
    }
}
