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

class Book extends Model
{
    public $fillable = ['title', 'isbn'];

    public function isbn()
    {
        return $this->hasOne(Isbn::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
