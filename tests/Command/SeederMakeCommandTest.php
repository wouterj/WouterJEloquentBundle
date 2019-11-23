<?php

namespace WouterJ\EloquentBundle\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeederMakeCommandTest extends TestCase
{
    use SetUpTearDownTrait;

    private $input;
    private $output;
    private $command;
    private $projectDir;

    protected function doSetUp()
    {
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->projectDir = sys_get_temp_dir().'/'.uniqid();

        mkdir($this->projectDir, 0777, true);

        $this->command = new SeederMakeCommand($this->projectDir.'/src', []);
    }

    /** @test */
    public function it_creates_app_seeders()
    {
        TestCommand::create($this->command)
            ->passing('name', 'App\Seed\PostSeeder')
            ->duringExecute();

        $this->assertSeederEquals('PostSeeder', $this->projectDir.'/src/Seed/PostSeeder.php');
    }

    /** @test */
    public function it_creates_bundle_seeders()
    {
        file_put_contents($this->projectDir.'/WouterJDemoBundle.php', '<?php namespace WouterJ\DemoBundle; class WouterJDemoBundle { }');

        require $this->projectDir.'/WouterJDemoBundle.php';

        $command = new SeederMakeCommand($this->projectDir, [\WouterJ\DemoBundle\WouterJDemoBundle::class]);

        TestCommand::create($command)
            ->passing('name', 'WouterJ\DemoBundle\Seed\PostSeeder')
            ->duringExecute();

        $this->assertFileExists($this->projectDir.'/Seed/PostSeeder.php');
    }

    /** @test */
    public function it_has_a_target_option()
    {
        TestCommand::create($this->command)
            ->passing('name', 'App\Seed\PostSeeder')
            ->passing('--target', $this->projectDir.'/seeds/PostSeeder.php')
            ->duringExecute();

        $this->assertFileExists($this->projectDir.'/seeds/PostSeeder.php');
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
