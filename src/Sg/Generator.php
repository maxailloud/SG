<?php

namespace Sg;

use Symfony\Component;

/**
 * Generator class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Generator extends \Sg\Outputter
{
    /** @var null|string */
    private $sourceDirectory        = null;

    /** @var null|string */
    private $destinationDirectory   = null;

    /** @var null|string */
    private $layoutFile             = null;

    /** @var null|string */
    private $pageDirectory          = null;

    /** @var null|\Sg\Configuration */
    private $configuration = null;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @return \Sg\Generator
     */
    public function __construct(Component\Console\Output\OutputInterface $output, $sourceDirectory, $destinationDirectory = null)
    {
        parent::__construct($output);

        $sourceDirectory        = realpath($sourceDirectory);
        $destinationDirectory   = (null !== $destinationDirectory) ? realpath($destinationDirectory) : null;

        $this->sourceDirectory      = (null !== $destinationDirectory) ? $sourceDirectory : $sourceDirectory . DIRECTORY_SEPARATOR . '.sg';
        $this->destinationDirectory = (null !== $destinationDirectory) ? $destinationDirectory : $sourceDirectory;

        $this->configuration = new Configuration($this->sourceDirectory);
    }

    /**
     * @return void
     */
    public function generate()
    {
        $this->outputln("<comment>Starting static site generation.</comment>");

        try
        {
            $this
                ->checkSourceDirectory($this->sourceDirectory)
                ->checkDestinationDirectory($this->destinationDirectory)
                ->checkLayoutFile()
                ->checkPagesDirectory()
            ;

            $assets = $this->configuration->getOption('assets');
            if(false === $assets)
            {
                $assetProcessor = new \Sg\Processor\Asset($this->getOutput());
                $assetProcessor->process($this->sourceDirectory, $this->destinationDirectory);
            }

            $templateProcessor = new \Sg\Processor\Template($this->getOutput());
            $templateProcessor->process($this->sourceDirectory, $this->destinationDirectory, $assets);
        }
        catch(\Exception $exception)
        {
            $this->outputResult(self::OUTPUT_FAIL, $exception->getMessage());
        }

        $this->outputln("<comment>Static site generation done.</comment>");
    }

    /**
     * @return \Sg\Generator
     * @throws \Exception
     */
    public function cleanFiles()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->files()->exclude('.sg')->in($this->destinationDirectory);

        $directories = array();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach($files as $file)
        {
            $filePath = $file->getPathName();
            if(true === is_dir($filePath))
            {
                $directories[] = $filePath;
            }
            else
            {
                if(false === unlink($filePath))
                {
                    throw new \Exception(sprintf("Unable to delete '%s' file.", $filePath));
                }
            }
        }

        foreach($directories as $directory)
        {
            if(false === rmdir($directory))
            {
                throw new \Exception(sprintf("Unable to delete '%s' directory.", $directory));
            }
        }

        $this->outputln('<comment>Cleaning files done.</comment>');

        return $this;
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

        $this->outputResult(self::OUTPUT_OK, sprintf('Layout file : %s', $this->layoutFile));

        return $this;
    }

    /**
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

        $this->outputResult(self::OUTPUT_OK, sprintf('Page directory : %s', $this->pageDirectory));

        return $this;
    }

    /**
     * @return \Sg\Generator
     * @throws \Exception
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

        $this->outputResult(self::OUTPUT_OK, sprintf('Source directory : %s', $this->sourceDirectory));

        return $this;
    }

    /**
     * @return \Sg\Generator
     * @throws \Exception
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

        $this->outputResult(self::OUTPUT_OK, sprintf('Destination directory : %s', $this->destinationDirectory));

        return $this;
    }
}