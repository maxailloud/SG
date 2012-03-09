<?php

namespace Sg;

use \Symfony\Component;

class Generator extends \Sg\Outputter
{
    const OUTPUT_OK         = '[<info>OK</info>]';
    const OUTPUT_FAIL       = '[<error>FAIL</error>]';
    const OUTPUT_COMMENT    = '[<comment>FAIL</comment>]';

    private $sourceDirectory        = null;
    private $destinationDirectory   = null;
    private $layoutFile             = null;
    private $pageDirectory          = null;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     */
    public function __construct(Component\Console\Output\OutputInterface $output, $sourceDirectory, $destinationDirectory)
    {
        parent::__construct($output);
        $this->sourceDirectory      = (DIRECTORY_SEPARATOR === substr($sourceDirectory, -1)) ? substr($sourceDirectory, 0, -1) : $sourceDirectory;
        $this->destinationDirectory = (DIRECTORY_SEPARATOR === substr($destinationDirectory, -1)) ? substr($destinationDirectory, 0, -1) : $destinationDirectory;
    }

    /**
     * @return void
     */
    public function generate()
    {
        $this->writeln("<comment>Starting static site generation.</comment>");

        try
        {
            $this
                ->checkSourceDirectory($this->sourceDirectory)
                ->checkDestinationDirectory($this->destinationDirectory)
                ->checkLayoutFile()
                ->checkPagesDirectory()
            ;

            $templateProcessor = new \Sg\Processor\Template($this->getOutput());
            $templateProcessor->process($this->sourceDirectory, $this->destinationDirectory);

            $assetProcessor = new \Sg\Processor\Asset($this->getOutput());
            $assetProcessor->process($this->sourceDirectory, $this->destinationDirectory);
        }
        catch(\Exception $exception)
        {
            $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
        }

        $this->writeln("<comment>Static site generation done.</comment>");
    }

    /**
     * Check if layout file exists in the source directory
     *
     * @return \Sg\Generator
     * @throws \Exception
     */
    public function checkLayoutFile()
    {
        $this->layoutFile = $this->sourceDirectory . DIRECTORY_SEPARATOR . 'layout.twig';

        if(false === is_file($this->layoutFile))
        {
            throw new \Exception(sprintf("The layout file '%s' doesn't exist.", $this->layoutFile));
        }

        if(false === is_readable($this->layoutFile))
        {
            throw new \Exception(sprintf("The layout file '%s' cannot be read.", $this->layoutFile));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('Layout file : %s', $this->layoutFile));

        return $this;
    }

    /**
     * Check if the 'pages' directory exists in the source directory
     *
     * @return \Sg\Generator
     * @throws \Exception
     */
    public function checkPagesDirectory()
    {
        $this->pageDirectory = $this->sourceDirectory . DIRECTORY_SEPARATOR . 'pages';

        if(false === is_dir($this->pageDirectory))
        {
            throw new \Exception(sprintf("The page directory '%s' doesn't exist.", $this->pageDirectory));
        }

        if(false === is_readable($this->pageDirectory))
        {
            throw new \Exception(sprintf("The page directory '%s' cannot be read.", $this->pageDirectory));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('Page directory : %s', $this->pageDirectory));

        return $this;
    }

    /**
     * @return \Sg\Generator
     * @throws \InvalidArgumentException
     */
    public function checkSourceDirectory()
    {
        if(false === is_dir($this->sourceDirectory))
        {
            throw new \Exception(sprintf("The directory '%s' doesn't exist.", $this->sourceDirectory));
        }

        if(false === is_readable($this->sourceDirectory))
        {
            throw new \Exception(sprintf("The directory '%s' cannot be read.", $this->sourceDirectory));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('Source directory : %s', $this->sourceDirectory));

        return $this;
    }

    /**
     * @return \Sg\Generator
     * @throws \InvalidArgumentException
     */
    public function checkDestinationDirectory()
    {
        if(false === is_dir($this->destinationDirectory))
        {
            throw new \Exception(sprintf("The destination directory '%s' doesn't exist.", $this->destinationDirectory));
        }

        if(false === is_readable($this->destinationDirectory))
        {
            throw new \Exception(sprintf("The destination directory '%s' cannot be read.", $this->destinationDirectory));
        }

        if(false === is_writable($this->destinationDirectory))
        {
            throw new \Exception(sprintf("You don't have the right permission to write into the destination directory '%s'.", $this->destinationDirectory));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('Destination directory : %s', $this->destinationDirectory));

        return $this;
    }
}