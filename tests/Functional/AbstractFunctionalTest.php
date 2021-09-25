<?php

namespace WouterJ\EloquentBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFunctionalTest extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    protected static function getKernelClass(): string
    {
        return 'TestKernel';
    }
}
