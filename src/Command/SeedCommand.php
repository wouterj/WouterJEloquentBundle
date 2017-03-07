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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WouterJ\EloquentBundle\Seeder;

/**
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class SeedCommand extends ContainerAwareCommand
{
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
            ])
        ;
    }

    public function execute(InputInterface $i, OutputInterface $o)
    {
        $seeders = $this->getSeeders($i->getArgument('class'));

        if (0 === count($seeders)) {
            throw new \RuntimeException('No Seeder classes found.');
        }

        $resolver = $this->getContainer()->get('wouterj_eloquent.database_manager');
        if (null !== $i->getOption('database')) {
            $resolver->setDefaultConnection($i->getOption('database'));
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

        foreach ($this->getContainer()->getParameter('kernel.bundles') as $bundle) {
            $class = substr($bundle, 0, strrpos($bundle, '\\')).'\Seed\DatabaseSeeder';

            if (class_exists($class)) {
                $seeders[] = $class;
            }
        }

        return $seeders;
    }

    private function resolve($class)
    {
        $s = new NoActionSeeder();
        $s->setSfContainer($this->getContainer());

        return $s->resolve($class);
    }
}

class NoActionSeeder extends Seeder
{
    public function run()
    { }
}
