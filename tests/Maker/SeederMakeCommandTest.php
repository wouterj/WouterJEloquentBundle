<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use WouterJ\EloquentBundle\MockeryTrait;

class SeederMakeCommandTest extends TestCase
{
    use MakerTestTrait;
    use MockeryTrait;

    protected $defaultOptions = ['command' => 'make:seeder'];
    private $fileManager;
    private $generator;

    protected function setUp(): void
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
        $fixturePath = file_get_contents(__DIR__.'/../Fixtures/seeds/'.$name.'.php');
        if (trait_exists(WithoutModelEvents::class)) {
            $fixturePath = file_get_contents(__DIR__.'/../Fixtures/seeds/'.$name.'.laravel9.php');
        }
        $normalizedExpected = preg_replace('/\R/', "\n", $fixturePath);

        $path = '/app/src/Seed/'.$name.'.php';
        $this->fileManager->allows()->getRelativePathForFutureClass()->with('App\\Seed\\'.$name)->andReturn($path);
        $this->fileManager->shouldReceive('dumpFile')->once()->with($path, $normalizedExpected);
    }
}
