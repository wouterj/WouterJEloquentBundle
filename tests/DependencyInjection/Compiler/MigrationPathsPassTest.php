<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection\Compiler;

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class MigrationPathsPassTest extends TestCase
{
    use SetUpTearDownTrait;

    private $container;

    protected function doSetUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->addCompilerPass(new MigrationPathsPass(), PassConfig::TYPE_OPTIMIZE);

        $this->container->register('wouterj_eloquent.migrator', __CLASS__.'_MigratorStub');
        $this->container->register('app.service', __CLASS__.'_ServiceStub')
            ->addArgument(new Reference('wouterj_eloquent.migrator'))
            ->setPublic(true)
        ;
    }

    /** @test */
    public function it_configures_the_extra_migration_paths()
    {
        MigrationPathsPass::add('/package1/migrations');
        MigrationPathsPass::add('/package2/Resources/migrations');

        $this->container->compile();

        $migrator = $this->container->get('app.service')->migrator;
        $this->assertEquals(['/package1/migrations', '/package2/Resources/migrations'], $migrator->paths());
    }
}

class MigrationPathsPassTest_ServiceStub
{
    public $migrator;

    public function __construct(MigrationPathsPassTest_MigratorStub $migrator)
    {
        $this->migrator = $migrator;
    }
}

class MigrationPathsPassTest_MigratorStub
{
    private $paths = [];

    public function __construct()
    {
        $this->paths = [];
    }

    public function path($path)
    {
        $this->paths[] = $path;
    }

    public function paths()
    {
        return $this->paths;
    }
}
