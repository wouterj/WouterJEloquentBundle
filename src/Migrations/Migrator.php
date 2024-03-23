<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Migrations;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The LaravelMigrator without the dependency on Laravel filesystem.
 *
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Migrator extends LaravelMigrator
{
    public function __construct(MigrationRepositoryInterface $repository, Resolver $resolver)
    {
        if (class_exists(Filesystem::class)) {
            parent::__construct($repository, $resolver, new Filesystem());
        } else {
            // BC with Laravel <10
            $this->repository = $repository;
            $this->resolver = $resolver;
            $this->files = new class extends Filesystem {
                public function getRequire($path, array $data = [])
                {
                    if (is_file($path)) {
                        $__path = $path;
                        $__data = $data;

                        return (static function () use ($__path, $__data) {
                            extract($__data, EXTR_SKIP);

                            return require $__path;
                        })();
                    }

                    throw new FileNotFoundException("File does not exist at path {$path}.");
                }
            };
        }
    }

    public function getMigrationFiles($paths): array
    {
        if (0 === count((array) $paths)) {
            return [];
        }

        $files = Finder::create()->name('*_*.php')->in($paths)->sortByName();

        if (0 === count($files)) {
            return [];
        }

        $migrations = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $migrations[str_replace('.php', '', $file->getBasename())] = $file->getRealPath();
        }

        return $migrations;
    }

    public function requireFiles(array $files): void
    {
        foreach ($files as $file) {
            require_once $file;
        }
    }
}
