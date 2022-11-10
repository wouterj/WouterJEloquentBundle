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

use Illuminate\Console\View\Components;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Seeder;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SeedCommand extends Command
{
    use ConfirmationTrait;
    use ViewComponentsTrait;

    private $container;
    private $resolver;
    private $bundles;
    private $kernelEnv;

    /** @psalm-suppress ContainerDependency */
    public function __construct(ContainerInterface $container, DatabaseManager $resolver, array $bundles, string $kernelEnv)
    {
        parent::__construct();

        $this->container = $container;
        $this->resolver = $resolver;
        $this->bundles = $bundles;
        $this->kernelEnv = $kernelEnv;
    }

    public function configure(): void
    {
        $this->setName('eloquent:seed')
            ->setDescription('Seed the database with records')
            ->setHelp(<<<EOT
The <info>%command.name%</info> seeds the database with records, specified by Seeders.

To execute all seeders, use it without arguments:

    <info>php %command.full_name%</info>

This will look for a <comment>DatabaseSeeder</comment> class in the <comment>Seed</comment> namespace of the
registered bundles.

To execute a specific Seeder, use it with the class name:

    <info>php %command.full_name% Acme\DemoBundle\Seed\UserTableSeeder</info>

If you want to use another connection, use the <comment>--database</comment> option:

    <info>php %command.full_name% --database test</info>
EOT
            )
            ->setDefinition([
                new InputArgument('class', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'The database seeder classes to run'),
                new InputOption('database', null, InputOption::VALUE_REQUIRED, 'The database connection to seed'),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Forces the operation to run when in production')
            ])
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force') && !$this->askConfirmationInProd($input, $output)) {
            return 1;
        }

        $this->info($output, 'Seeding database.');

        $seeders = $this->getSeeders($input->getArgument('class'));

        if (0 === count($seeders)) {
            $this->error($output, 'No Seeder classes found.');

            return 1;
        }

        if (null !== $input->getOption('database')) {
            $this->resolver->setDefaultConnection($input->getOption('database'));
        }

        foreach ($seeders as $seederClass) {
            $seeder = $this->resolve($seederClass);

            $this->task($output, $seederClass, function () use ($seeder): void {
                $seeder->run();
            });

            $seeds = $seeder->getSeedClasses();
            $last = array_key_last($seeds);
            foreach ($seeds as $i => $class) {
                if (class_exists(Components\TwoColumnDetail::class)) {
                    (new Components\TwoColumnDetail($output))->render('<fg=gray>'.($i == $last ? '└' : '├').'─ </>'.$class, '<fg=green;options=bold>DONE</>');
                } else {
                    // BC Laravel <9.39
                    $output->writeln('<fg=green;options=bold>DONE</>: '.$class);
                }
            }
        }

        $output->writeln('');

        return 0;
    }

    private function getSeeders(array $classes = []): array
    {
        if (0 === count($classes)) {
            return $this->getDatabaseSeederFromBundles();
        }

        $seeders = [];
        foreach ($classes as $class) {
            if (class_exists($class)) {
                $seeders[] = $class;
            }
        }

        return $seeders;
    }

    private function getDatabaseSeederFromBundles(): array
    {
        $seeders = [];

        foreach ($this->bundles as $bundle) {
            $class = substr($bundle, 0, strrpos($bundle, '\\')).'\Seed\DatabaseSeeder';

            if (class_exists($class)) {
                $seeders[] = $class;
            }
        }

        // add application seeder when using Symfony Flex
        if (class_exists('App\Seed\DatabaseSeeder')) {
            $seeders[] = 'App\Seed\DatabaseSeeder';
        }

        return $seeders;
    }

    private function resolve(string $class): Seeder
    {
        $s = new class extends Seeder {
            public function run()
            { }
        };
        $s->setSfContainer($this->container);

        return $s->resolve($class);
    }
}
