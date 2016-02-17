<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new WouterJEloquentExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    /**
     * @test
     * @dataProvider provideFormattingData
     */
    public function it_works_with_different_formats($source, $expectedConfiguration)
    {
        $this->assertProcessedConfigurationEquals($expectedConfiguration, [$source]);
    }

    public function provideFormattingData()
    {
        $expected1 = [
            'aliases' => [
                'db' => false,
                'schema' => false,
            ],
            'connections' => [],
            'eloquent' => ['enabled' => false],
            'default_connection' => 'default',
        ];
        $expected2 = [
            'aliases' => [
                'db' => false,
                'schema' => true,
            ],
            'connections' => [
                'default' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'database',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                ],
            ],
            'default_connection' => 'default',
            'eloquent' => ['enabled' => false],
        ];
        $expected3 = [
            'aliases' => [
                'db' => true,
                'schema' => true,
            ],
            'connections' => [
                'default' => [
                    'driver' => 'sqlite',
                    'host' => 'local',
                    'database' => 'foo.db',
                    'username' => 'user',
                    'password' => 'pass',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => 'symfo_',
                ],
            ],
            'default_connection' => 'default',
            'eloquent' => ['enabled' => true],
        ];
        $expected4 = [
            'aliases' => [
                'db' => false,
                'schema' => false,
            ],
            'connections' => [
                'default' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'database',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                ],
                'foo' => [
                    'driver' => 'sqlite',
                    'host' => 'local',
                    'database' => 'foo.db',
                    'username' => 'user',
                    'password' => 'pass',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => 'symfo_',
                ],
            ],
            'default_connection' => 'foo',
            'eloquent' => ['enabled' => false],
        ];

        $path = function ($file) {
            return __DIR__.'/../Fixtures/config/'.$file;
        };

        return [
            [$path('config1.yml'), $expected1],
            [$path('config1.xml'), $expected1],
            [$path('config1.php'), $expected1],

            [$path('config2.yml'), $expected2],
            [$path('config2.xml'), $expected2],
            [$path('config2.php'), $expected2],

            [$path('config3.yml'), $expected3],
            [$path('config3.xml'), $expected3],
            [$path('config3.php'), $expected3],

            [$path('config4.yml'), $expected4],
            [$path('config4.xml'), $expected4],
            [$path('config4.php'), $expected4],
        ];
    }
}
