<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Seeder as IlluminateSeeder;
use Illuminate\Database\Console\DumpCommand;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use WouterJ\EloquentBundle\Seeder;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class MakeSeeder extends AbstractMaker
{
    private $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public static function getCommandName(): string
    {
        return 'make:seeder';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Create a new Eloquent seeder class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the seeder class')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
        if (false !== strpos($name, '\\') && '\\' !== $name[0]) {
            $name = '\\'.$name;
        }

        $seederClassDetails = $generator->createClassNameDetails($name, 'Seed', 'Seeder');

        if (class_exists($seederClassDetails->getFullName())) {
            $io->error(sprintf('Seeder "%s" already exists!', $seederClassDetails->getFullName()));

            return;
        }

        $stubPath = dirname((new \ReflectionClass(IlluminateSeeder::class))->getFileName()).'/Console/Seeds/stubs';
        $stub = file_get_contents($stubPath.'/seeder.stub');
        $stub = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $seederClassDetails->getShortName(), $stub);
        $stub = str_replace(IlluminateSeeder::class, Seeder::class, $stub);

        if ($namespace = Str::getNamespace($seederClassDetails->getFullName())) {
            if (class_exists(DumpCommand::class)) {
                // Laravel 8
                $stub = str_replace('namespace Database\Seeders;', 'namespace '.$namespace.';', $stub);
            } else {
                // Laravel 6 & 7
                $stub = str_replace('<?php', "<?php\n\nnamespace ".$namespace.';', $stub);
            }
        }

        $path = $this->fileManager->getRelativePathForFutureClass($seederClassDetails->getFullName());
        if (null === $path) {
            throw new \LogicException(sprintf('Could not determine where to locate the new class "%s", maybe try with a full namespace like "\\My\\Full\\Namespace\\%s"', $seederClassDetails->getFullName(), $seederClassDetails->getShortName()));
        }

        $this->fileManager->dumpFile($path, $stub);
    }
}
