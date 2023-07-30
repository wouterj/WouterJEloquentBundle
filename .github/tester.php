<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

require_once __DIR__.'/../vendor/autoload.php';

(new SingleCommandApplication())->setCode(function (InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);
    $helper = $this->getHelper('process');

    $io->section('Setting up the project...');

    $projectDir = sys_get_temp_dir().'/eloquent';
    $refDir = $projectDir.'-ref';
    $filesystem = new Filesystem();
    if ($filesystem->exists($projectDir)) $filesystem->remove($projectDir);
    if ($filesystem->exists($refDir)) $filesystem->remove($refDir);

    $helper->mustRun($output, new Process(['git', 'clone', 'https://github.com/wouterj-nl/symfony-eloquent.git', $refDir]));
    $filesystem->mkdir($projectDir);
    $helper->mustRun($output, new Process(['git', 'init'], $projectDir));

    $commits = array_map(
        fn ($l) => explode(' ', $l, 2),
        array_reverse(
            preg_split(
                '/\R/',
                trim(
                    (new Process(['git', 'log', '--oneline'], $refDir))->mustRun()->getOutput()
                )
            )
        )
    );

    $io->writeln('');

    $io->section('Running the test');

    $i = 1;
    foreach ($commits as [$commit, $msg]) {
        [$op, $val] = explode(': ', $msg, 2);
        switch ($op) {
            case 'PATCH':
                $io->text($i++.'. Patching files ('.$commit.'): '.$val);
                $diff = (new Process(['git', 'show', '--pretty=format:%b', $commit], $refDir))->mustRun()->getOutput();
                $helper->mustRun($output, (new Process(['git', 'apply'], $projectDir))->setInput($diff));

                break;
            case 'CMD':
                $io->text($i++.'. Running command: <comment>'.$val.'</>');
                $helper->mustRun($output, (Process::fromShellCommandline($val, $projectDir, ['SHELL_VERBOSITY' => '0'])));

                break;
            default:
                throw new \LogicException('Unknown operation: '.$op);
        }
    }
})->run();
