<?php

namespace WouterJ\EloquentBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MakeModel extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:model';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Create a new Eloquent model class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the model')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists')
            ->addOption('pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $modelClassDetails = $generator->createClassNameDetails($input->getArgument('name'), 'Model');
        if (class_exists($modelClassDetails->getFullName()) && !$input->getOption('force')) {
            $io->error(sprintf('Model "%s" already exists!', $modelClassDetails->getFullName()));

            return;
        }

        $generator->generateClass($modelClassDetails->getFullName(), __DIR__.'/../../templates/stubs/model.tpl.php');

        $generator->writeChanges();
    }
}
