<?php

namespace Sg\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generation command')
            ->addArgument('directory', InputArgument::REQUIRED, 'The target directory')
            ->setHelp(<<<EOT
The <info>generate</info> command generates your files into the given directory.

<info>php sg generate directory</info>

The target directory will be created if it doesn't exists, and the
files will be generated into it.

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeSection($output, 'Generating files in progress ...');
    }
}
