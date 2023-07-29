<?php

namespace WouterJ\EloquentBundle\Maker;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Casts\Json;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use WouterJ\EloquentBundle\MockeryTrait;

class MakeFactoryTest extends TestCase
{
    use MakerTestTrait;
    use MockeryTrait;

    protected $defaultOptions = ['command' => 'make:factory'];
    private $fileManager;
    private $generator;

    protected function setUp(): void
    {
        $this->fileManager = \Mockery::spy(FileManager::class);
        $this->maker = new MakeFactory($this->fileManager);
        $this->generator = new Generator($this->fileManager, 'App');
    }

    /**
     * @test
     * @dataProvider providePostFactoryNames
     */
    public function it_creates_factories($name)
    {
        $this->expectFactory('PostFactory');

        $this->callGenerate(['name' => $name]);
    }

    public function providePostFactoryNames()
    {
        yield ['Post'];
        yield ['PostFactory'];
        yield ['\App\Factory\PostFactory'];
    }

    /**
     * @test
     */
    public function it_accepts_model_fqcn()
    {
        $this->expectFactory('PersonFactory');

        $this->callGenerate(['name' => 'PersonFactory', '--model' => 'Person']);
    }

    /**
     * @test
     * @dataProvider provideFactoryNames
     */
    public function it_guesses_model_fqcn($name)
    {
        if (!class_exists('App\Model\Talk')) {
            eval('namespace App\Model { class Talk {} }');
        }

        $this->expectFactory('TalkFactory');

        $this->callGenerate(['name' => $name]);
    }

    public function provideFactoryNames()
    {
        yield ['Talk'];
        yield ['TalkFactory'];
        yield ['\App\Factory\TalkFactory'];
    }

    private function expectFactory(string $name)
    {
        $fixturePath = __DIR__.'/../Fixtures/factories/'.$name;
        // BC with Laravel <10
        if (!trait_exists(WithoutModelEvents::class)) {
            $fixturePath .= '-8';
        } elseif (!class_exists(Json::class)) {
            $fixturePath .= '-9';
        }
        $fixturePath .= '.php';
        $normalizedExpected = preg_replace('/\R/', "\n", file_get_contents($fixturePath));

        $path = '/app/src/Factory/'.$name.'.php';
        $this->fileManager->allows()->getRelativePathForFutureClass()->with('App\\Factory\\'.$name)->andReturn($path);
        $this->fileManager->expects()->dumpFile()->once()->with($path, $normalizedExpected);
    }
}
