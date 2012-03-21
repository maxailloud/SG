<?php

namespace Sg\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PharCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this
            ->setName('build-phar')
            ->setDescription('Build the PHAR archive of SG.')
            ->setHelp(<<<EOT
The <info>build-phar</info> command build the PHAR archive of SG.
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
        $builder = new \Sg\Phar\Builder($output);
        $builder->build();
    }
}
