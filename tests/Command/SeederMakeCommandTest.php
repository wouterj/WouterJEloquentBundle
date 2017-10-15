<?php

namespace WouterJ\EloquentBundle\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeederMakeCommandTest extends TestCase
{
    private $container;
    private $input;
    private $output;
    private $subject;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->getParameter('kernel.bundles')->willReturn([]);
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->subject = new SeederMakeCommand();
        $this->subject->setContainer($this->container->reveal());
    }

    /** @test */
    public function it_creates_app_seeders()
    {
        $appRoot = sys_get_temp_dir().'/'.uniqid();

        $this->container->getParameter('kernel.root_dir')->willReturn($appRoot);

        TestCommand::create($this->subject)
            ->passing('name', 'App\Seed\PostSeeder')
            ->duringExecute();

        $this->assertSeederEquals('PostSeeder', $appRoot.'/src/App/Seed/PostSeeder.php');
    }

    /** @test */
    public function it_creates_bundle_seeders()
    {
        $appRoot = sys_get_temp_dir().'/'.uniqid();
        mkdir($appRoot);

        file_put_contents($appRoot.'/WouterJDemoBundle.php', '<?php namespace WouterJ\DemoBundle; class WouterJDemoBundle { }');

        require $appRoot.'/WouterJDemoBundle.php';

        $this->container->getParameter('kernel.bundles')->willReturn([\WouterJ\DemoBundle\WouterJDemoBundle::class]);

        TestCommand::create($this->subject)
            ->passing('name', 'WouterJ\DemoBundle\Seed\PostSeeder')
            ->duringExecute();

        $this->assertFileExists($appRoot.'/Seed/PostSeeder.php');
    }

    /** @test */
    public function it_has_a_target_option()
    {
        $appRoot = sys_get_temp_dir().'/'.uniqid();

        TestCommand::create($this->subject)
            ->passing('name', 'App\Seed\PostSeeder')
            ->passing('--target', $appRoot.'/seeds/PostSeeder.php')
            ->duringExecute();

        $this->assertFileExists($appRoot.'/seeds/PostSeeder.php');
    }

    private function assertSeederEquals($expected, $actual)
    {
        $this->assertFileExists($actual);

        $normalize = function ($str) { return preg_replace('/\R/', "\n", $str); };

        $normalizedExpected = $normalize(file_get_contents(__DIR__.'/../Fixtures/seeds/'.$expected.'.php'));
        $normalizedActual = $normalize(file_get_contents($actual));

        $this->assertEquals($normalizedExpected, $normalizedActual);
    }
}
