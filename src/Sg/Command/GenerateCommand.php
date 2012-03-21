<?php

namespace Sg\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generation command')
            ->addArgument('sourceDirectory', InputArgument::REQUIRED, 'The source directory (and the destination directory too if destination directory not specified)')
            ->addArgument('destinationDirectory', InputArgument::OPTIONAL, 'The destination directory')
            ->setHelp(<<<EOT
The <info>generate</info> command generates from your files in the source directory to the destination directory.

<info>php sg generate source destination</info>

The target directory will be created if it doesn't exists, and the files will be generated into it.

<info>php sg generate directory</info>

If just one directory is given the generation files must be in a <comment>'directory/.sg'</comment> directory.

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
        $generator = new \Sg\Generator($output, $input->getArgument('sourceDirectory'), $input->getArgument('destinationDirectory'));
        $generator
            ->setOutput($output)
            ->generate()
        ;
    }
}
