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

use Illuminate\Database\Migrations\MigrationCreator;

/**
 * A bridge between Illuminate\Database and Symfony.
 *
 * This removes the dependency on Illuminate\Filesystem in favor
 * of PHP's file_*_contents() functions for filesystem tasks.
 *
 * @final
 * @internal
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class Creator extends MigrationCreator
{
    // Override constructor to remove Illuminate\Filesystem dep
    public function __construct()
    { }

    /** {@inheritdoc} */
    public function create($name, $path, $table = null, $create = false)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $path = $this->getPath($name, $path);
        $stub = $this->getStub($table, $create);

        file_put_contents($path, $this->populateStub($name, $stub, $table));

        $this->firePostCreateHooks($table);

        return $path;
    }

    /** {@inheritdoc} */
    protected function getStub($table, $create)
    {
        $file = 'blank.stub';
        if (null !== $table) {
            $file = $create ? 'create.stub' : 'update.stub';
        }

        $stubContents = file_get_contents($this->stubPath().'/'.$file);
        $stubContents = str_replace('Illuminate\Support\Facades', 'WouterJ\EloquentBundle\Facade', $stubContents);

        return $stubContents;
    }
}
