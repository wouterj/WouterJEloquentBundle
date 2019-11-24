<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Functional;

use AppBundle\Model\Book;
use AppBundle\Model\Category;
use AppBundle\Model\Conn2Book;
use AppBundle\Model\Isbn;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WouterJ\EloquentBundle\Facade\Schema;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class ConnectionsTest extends KernelTestCase
{
    use SetUpTearDownTrait;

    protected static function getKernelClass()
    {
        return 'TestKernel';
    }

    protected function doSetUp()
    {
        static::bootKernel();

        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('isbn_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->timestamps();
        });

        Schema::connection('conn2')->create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('isbn_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->timestamps();
        });
    }

    public function testMultipleConnections()
    {
        $book1 = new Book;
        $book1->title = 'Hello world!';
        $book1->save();

        $book2 = new Conn2Book;
        $book2->title = 'Other world!';
        $book2->save();

        $this->assertEquals(1, Book::all()->count());
        $this->assertEquals('Hello world!', Book::all()->first()->title);

        $this->assertEquals(1, Conn2Book::all()->count());
        $this->assertEquals('Other world!', Conn2Book::all()->first()->title);
    }
}
