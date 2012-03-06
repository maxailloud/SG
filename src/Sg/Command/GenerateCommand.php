<?php

namespace Sg\Command;

use Sg;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generation command')
            ->addArgument('sourceDirectory', InputArgument::REQUIRED, 'The source directory')
            ->addArgument('destinationDirectory', InputArgument::REQUIRED, 'The destination directory')
            ->setHelp(<<<EOT
The <info>generate</info> command generates your files into the given directory.

<info>php sg generate directory</info>

The target directory will be created if it doesn't exists, and the
files will be generated into it.

EOT
            )
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');

        $generator = new \Sg\Generator($input->getArgument('sourceDirectory'), $input->getArgument('destinationDirectory'));
        $generator
            ->setOuput($output)
            ->generate()
        ;
    }
}
