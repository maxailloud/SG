<?php

namespace Sg\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate site command class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class GenerateCommand extends SymfonyCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generation command')
            ->addArgument('sourceDirectory', InputArgument::OPTIONAL, 'The source directory (and the destination directory too if destination directory not specified)')
            ->addArgument('destinationDirectory', InputArgument::OPTIONAL, 'The destination directory')
            ->addOption('regenerate', 'R', InputOption::VALUE_NONE, 'If specified delete all previously generate files before generation')
            ->setHelp(<<<EOT
The <info>generate</info> command generates your site from your files in the source directory to the destination directory.

<info>php sg generate</info>

Calling the command whitout argument will generate your site in the current directory.
The source of your site must be in the <comment>'.sg'</comment> directory.

<info>php sg generate directory</info>

Calling the command whitout argument will generate your site in the given directory.
The source of your site must be in the <comment>'directory/.sg'</comment> directory.

<info>php sg generate source destination</info>

Calling the command whitout argument will generate your site in the destination directory.
The source of your site must be in the <comment>'source'</comment> directory.

To delete all files before generation, include the <comment>--regenerate (-R)</comment> option:

<info>php sg generate --regenerate(-R) directory</info>
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
        $sourceDirectory = (null === $input->getArgument('sourceDirectory')) ? '.' : $input->getArgument('sourceDirectory');

        $generator = new \Sg\Generator($output, $sourceDirectory, $input->getArgument('destinationDirectory'));

        $generator->setOutput($output);

        if(true === $input->getOption('regenerate'))
        {
            $generator->cleanFiles();
        }

        $generator->generate();
    }
}
