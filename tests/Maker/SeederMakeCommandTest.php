<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Casts\Json;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
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

        $this->generator = new Generator($this->fileManager, 'App');
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
        $fixturePath = __DIR__.'/../Fixtures/seeds/'.$name.'.php';
        if (class_exists(Json::class)) {
            $fixturePath = __DIR__.'/../Fixtures/seeds/'.$name.'.laravel10.php';
        } elseif (trait_exists(WithoutModelEvents::class)) {
            $fixturePath = __DIR__.'/../Fixtures/seeds/'.$name.'.laravel9.php';
        }
        $normalizedExpected = preg_replace('/\R/', "\n", file_get_contents($fixturePath));

        $path = '/app/src/Seed/'.$name.'.php';
        $this->fileManager->allows()->getRelativePathForFutureClass()->with('App\\Seed\\'.$name)->andReturn($path);
        $this->fileManager->shouldReceive('dumpFile')->once()->with($path, $normalizedExpected);
    }
}
