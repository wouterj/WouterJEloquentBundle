<?php

namespace WouterJ\EloquentBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataCollectorTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return 'TestKernel';
    }

    public function testDataCollectorRegistered()
    {
        if (method_exists(__CLASS__, 'getContainer')) {
            $container = self::getContainer();
        } else {
            static::bootKernel();
            $container = self::$kernel->getContainer();
        }

        $this->assertArrayHasKey('wouterj_eloquent.data_collector', array_merge($container->getServiceIds(), $container->getRemovedIds()));
    }
}
