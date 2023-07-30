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
        if (class_exists(Components\Info::class)) {
            (new Components\Info($output))->render($message);
        } else {
            // BC Laravel <9.39
            $output->writeln('<info>'.$message.'</>');
        }
    }

    protected function error(OutputInterface $output, string $message): void
    {
        if (class_exists(Components\Error::class)) {
            (new Components\Error($output))->render($message);
        } else {
            // BC Laravel <9.39
            $output->writeln('<error>'.$message.'</>');
        }
    }

    protected function task(OutputInterface $output, string $description, callable $task): void
    {
        if (class_exists(Components\Task::class)) {
            (new Components\Task($output))->render($description, $task);
        } else {
            // BC Laravel <9.39
            $startTime = microtime(true);

            $output->writeln('<info>RUNNING</info>: '.$description);

            $task();

            $runTime = number_format((microtime(true) - $startTime) * 1000).'ms';

            $output->writeln('<fg=green;options=bold>DONE</>: '.$description.' <fg=gray>('.$runTime.')</>');
        }
    }
} 
