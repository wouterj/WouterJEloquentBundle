<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Eloquent\Factories\Factory as IlluminateFactory;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use WouterJ\EloquentBundle\Factory\Factory;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MakeFactory extends AbstractMaker
{
    private $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public static function getCommandName(): string
    {
        return 'make:factory';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Eloquent model factory';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the factory')
            ->addOption('model', 'm', InputOption::VALUE_REQUIRED, 'The name of the model')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $factoryClassDetails = $generator->createClassNameDetails($input->getArgument('name'), 'Factory', 'Factory');
        if (class_exists($factoryClassDetails->getFullName())) {
            $io->error(sprintf('Factory "%s" already exists!', $factoryClassDetails->getFullName()));

            return;
        }

        $factoryFqcn = $factoryClassDetails->getFullName();
        $factory = $factoryClassDetails->getRelativeNameWithoutSuffix();

        $modelFqcn = $generator->createClassNameDetails($input->getOption('model') ?? $this->guessModelName($generator, $factoryClassDetails), 'Model')->getFullName();
        $model = Str::getShortClassName($modelFqcn);

        $stubPath = dirname((new \ReflectionClass(FactoryMakeCommand::class))->getFileName()).'/stubs';
        $stub = file_get_contents($stubPath.'/factory.stub');

        $replace = [
            'NamespacedDummyModel' => $modelFqcn,
            '{{ namespacedModel }}' => $modelFqcn,
            '{{namespacedModel}}' => $modelFqcn,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
            IlluminateFactory::class => Factory::class
        ];

        if ($namespace = Str::getNamespace($factoryFqcn)) {
            $replace['{{ factoryNamespace }}'] = $namespace;
        }

        $stub = str_replace(array_keys($replace), array_values($replace), $stub);

        $path = $this->fileManager->getRelativePathForFutureClass($factoryFqcn);
        if (null === $path) {
            throw new \LogicException(sprintf('Could not determine where to locate the new class "%s", maybe try with a full namespace like "\\My\\Full\\Namespace\\%s"', $factoryFqcn, $factoryClassDetails->getShortName()));
        }

        $this->fileManager->dumpFile($path, $stub);
    }

    private function guessModelName(Generator $generator, ClassNameDetails $factoryClassDetails): string
    {
        $name = $factoryClassDetails->getRelativeNameWithoutSuffix();

        $modelClassDetails = $generator->createClassNameDetails($name, 'Model');

        if (class_exists($modelClassDetails->getFullName())) {
            return '\\'.$modelClassDetails->getFullName();
        }


        return '\\'.$generator->getRootNamespace().'\\Model';
    }
}
