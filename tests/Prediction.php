<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle;

use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class Prediction
{
    public static function outputWritesLine(ObjectProphecy $output, $line)
    {
        $output->writeln($line)->shouldBeCalled();
    }
}
