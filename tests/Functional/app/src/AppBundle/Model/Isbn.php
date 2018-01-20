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

class Isbn extends Model
{
    public $fillable = ['nr'];
    public $timestamps = false;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
