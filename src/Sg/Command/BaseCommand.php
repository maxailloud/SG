<?php

namespace Sg\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends SymfonyCommand
{
    /**
     * Comes from the PropelBundle.
     * @see https://github.com/propelorm/PropelBundle/blob/master/Command/AbstractPropelCommand.php#L453
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output.
     * @param string $text  A text message.
     * @param string $style A style to apply on the section.
     *
     * @return void
     */
    protected function writeSection(OutputInterface $output, $text, $style = 'bg=blue;fg=white')
    {
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock($text, $style, true),
            '',
        ));
    }

    /**
     * Comes from the PropelBundle.
     * @see https://github.com/propelorm/PropelBundle/blob/master/Command/AbstractPropelCommand.php#L469
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output
     * @param string $taskName A task name
     *
     * @return void
     */
    protected function writeTaskError($output, $taskName)
    {
        return $this->writeSection($output, array(
            '[SG] Error',
            '',
            'An error has occured during the "' . $taskName . '" task process.'
        ), 'fg=white;bg=red');
    }
}
