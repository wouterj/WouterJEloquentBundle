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
use AppBundle\Model\Isbn;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WouterJ\EloquentBundle\Facade\Schema;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class RelationshipsTest extends KernelTestCase
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

        Schema::create('isbns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nr');
            $table->integer('book_id');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }

    public function testOneToOne()
    {
        $book = new Book;
        $book->title = 'Hello world';
        $book->save();

        $book->isbn()->save(new Isbn(['nr' => '978-90-274-3964-2']));

        $this->assertEquals('978-90-274-3964-2', Book::where('title', 'Hello world')->get()->first()->isbn->nr);
    }

    public function testOneToMany()
    {
        $category = Category::create(['name' => 'Science fiction']);
        $category->save();

        $book1 = new Book;
        $book1->title = 'The Second World!';
        $book1->category()->associate($category);
        $book1->save();

        $book2 = new Book;
        $book2->title = 'Is There a Third World?';
        $book2->category()->associate($category);
        $book2->save();

        $this->assertEquals('Science fiction', Book::where('title', 'The Second World!')->get()->first()->category->name);
        $this->assertEquals(['The Second World!', 'Is There a Third World?'], $category->books->map(function ($book) { return $book->title; })->toArray());
    }
}
