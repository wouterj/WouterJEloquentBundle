<?php

namespace WouterJ\EloquentBundle\Maker;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use WouterJ\EloquentBundle\MockeryTrait;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeederMakeCommandTest extends TestCase
{
    use MakerTestTrait;
    use SetUpTearDownTrait, MockeryTrait {
        MockeryTrait::doTearDown insteadof SetUpTearDownTrait;
    }

    protected $defaultOptions = ['command' => 'make:seeder'];
    private $fileManager;
    private $generator;

    protected function doSetUp()
    {
        $this->fileManager = \Mockery::spy(FileManager::class);
        $this->maker = new MakeSeeder($this->fileManager);
        $this->generator = new Generator($this->fileManager, 'App', class_exists(PhpCompatUtil::class) ? new PhpCompatUtil($this->fileManager) : null);
    }

    /**
     * @test
     * @dataProvider provideSeederNames
     */
    public function it_creates_app_seeders($name)
    {
        $this->expectSeeder('PostSeeder');

        $this->callGenerate(['name' => $name]);
    }

    public function provideSeederNames()
    {
        yield ['Post'];
        yield ['PostSeeder'];
        yield ['App\Seed\PostSeeder'];
    }

    private function expectSeeder(string $name)
    {
        $normalizedExpected = preg_replace('/\R/', "\n", file_get_contents(__DIR__.'/../Fixtures/seeds/'.$name.'.php'));

        $path = '/app/src/Seed/'.$name.'.php';
        $this->fileManager->allows()->getRelativePathForFutureClass()->with('App\\Seed\\'.$name)->andReturn($path);
        $this->fileManager->shouldReceive('dumpFile')->once()->with($path, $normalizedExpected);
    }
}
