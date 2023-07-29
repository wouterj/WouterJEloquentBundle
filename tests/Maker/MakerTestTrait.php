<?php

namespace WouterJ\EloquentBundle\Maker;

use Symfony\Bundle\MakerBundle\Command\MakerCommand;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Bundle\MakerBundle\Util\TemplateLinter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

trait MakerTestTrait
{
    /** @var MakerInterface */
    private $maker;

    private function callGenerate(array $input)
    {
        $app = new Application();
        $app->setAutoExit(false);
        $app->add(
            (new MakerCommand($this->maker, \Mockery::spy(FileManager::class), $this->generator ?? \Mockery::spy(Generator::class), class_exists(TemplateLinter::class) ? new TemplateLinter() : null))
                ->setName($this->maker->getCommandName())
        );

        $tester = new ApplicationTester($app);
        $exitCode = $tester->run(array_merge($this->defaultOptions ?? [], $input));

        $this->assertEquals(0, $exitCode, 'The process did not end successfully:'.$tester->getDisplay());
    }
}
