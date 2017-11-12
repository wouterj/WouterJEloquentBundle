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

use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WouterJ\EloquentBundle\Seeder;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SeedCommand extends Command
{
    /** @var ContainerInterface */
    private $container;
    /** @var DatabaseManager */
    private $resolver;
    /** @var array */
    private $bundles;
    /** @var */
    private $kernelEnv;

    public function __construct(ContainerInterface $container, DatabaseManager $resolver, array $bundles, $kernelEnv)
    {
        parent::__construct();

        $this->container = $container;
        $this->resolver = $resolver;
        $this->bundles = $bundles;
        $this->kernelEnv = $kernelEnv;
    }

    public function configure()
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

    public function execute(InputInterface $i, OutputInterface $o)
    {
        if (!$i->getOption('force') && !$this->askConfirmationInProd($i, $o)) {
            return;
        }

        $seeders = $this->getSeeders($i->getArgument('class'));

        if (0 === count($seeders)) {
            throw new \RuntimeException('No Seeder classes found.');
        }

        if (null !== $i->getOption('database')) {
            $this->resolver->setDefaultConnection($i->getOption('database'));
        }

        foreach ($seeders as $seederClass) {
            $seeder = $this->resolve($seederClass);
            $seeder->run();

            $o->writeln('<info>Seeded:</info> '.$seederClass);
            foreach ($seeder->getSeedClasses() as $class) {
                $o->writeln('<info>Seeded:</info> '.$class);
            }
        }
    }

    private function getSeeders($classes = null)
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

    private function getDatabaseSeederFromBundles()
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

    private function resolve($class)
    {
        $s = new NoActionSeeder();
        $s->setSfContainer($this->container);

        return $s->resolve($class);
    }

    private function askConfirmationInProd(InputInterface $i, OutputInterface $o)
    {
        if ('prod' !== $this->kernelEnv) {
            return true;
        }

        return $this->getHelper('question')
            ->ask($i, $o, new ConfirmationQuestion('Are you sure you want to execute the migrations in production?', false));
    }
}

class NoActionSeeder extends Seeder
{
    public function run()
    { }
}
