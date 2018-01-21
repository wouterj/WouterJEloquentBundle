<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Model;

use Illuminate\Database\Eloquent\Model;

class Conn2Book extends Model
{
    public $table = 'books';
    public $connection = 'conn2';
    public $fillable = ['title'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
