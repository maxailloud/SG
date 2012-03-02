<?php

namespace Sg\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Command test')
            ->setHelp(<<<EOT
The <info>assets:install</info> command installs bundle assets into a given
directory (e.g. the web directory).

<info>php app/console assets:install web [--symlink]</info>

A "bundles" directory will be created inside the target directory, and the
"Resources/public" directory of each bundle will be copied into it.

To create a symlink to each bundle instead of copying its assets, use the
<info>--symlink</info> option.

EOT
            )
        ;
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('test');
    }
}
