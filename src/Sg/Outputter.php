<?php

namespace Sg;

use Symfony\Component;

/**
 * Outputter class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Outputter
{
    const OUTPUT_OK         = '[<info>OK</info>]';
    const OUTPUT_FAIL       = '[<error>FAIL</error>]';
    const OUTPUT_COMMENT    = '[<comment>FAIL</comment>]';

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    private $output = null;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Sg\Generator
     */
    public function setOutput(Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $result
     * @param string $message
     * @param int $type
     * @return Generator
     */
    public function outputResult($result, $message, $type = 0)
    {
        $writeMessage = sprintf("%s - %s", str_pad($result, 19, ' ', \STR_PAD_RIGHT), $message);
        $this->output->writeln($writeMessage, $type);
    }

    /**
     * @param $messages
     * @param bool $newline
     * @param int $type
     */
    public function output($messages, $newline = false, $type = 0)
    {
        $this->output->write($messages, $newline, $type);
    }

    /**
     * @param string $messages
     * @param int $type
     */
    public function outputln($messages, $type = 0)
    {
        $this->output($messages, true, $type);
    }
}
