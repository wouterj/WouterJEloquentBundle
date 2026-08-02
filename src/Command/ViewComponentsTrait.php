<?php

/*
 * This file is part of the WouterJEloquentBundle package.
 *
 * (c) 2014 Wouter de Jong
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WouterJ\EloquentBundle\Command;

use Illuminate\Console\View\Components;
use Symfony\Component\Console\Output\OutputInterface;

trait ViewComponentsTrait
{
    protected function info(OutputInterface $output, string $message): void
    {
        (new Components\Info($output))->render($message);
    }

    protected function error(OutputInterface $output, string $message): void
    {
        (new Components\Error($output))->render($message);
    }

    protected function task(OutputInterface $output, string $description, callable $task): void
    {
        (new Components\Task($output))->render($description, $task);
    }
} 
