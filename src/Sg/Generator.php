<?php

namespace Sg;

use \Symfony\Component;

class Generator
{
    const OUTPUT_OK     = '[<info>OK</info>]';
    const OUTPUT_FAIL   = '[<error>FAIL</error>]';

    private $sourceDirectory        = null;
    private $destinationDirectory   = null;
    private $layoutFile             = null;
    private $pageDirectory          = null;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    private $output = null;

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     */
    public function __construct($sourceDirectory, $destinationDirectory)
    {
        $this->sourceDirectory      = (DIRECTORY_SEPARATOR === substr($sourceDirectory, -1)) ? substr($sourceDirectory, 0, -1) : $sourceDirectory;
        $this->destinationDirectory = (DIRECTORY_SEPARATOR === substr($destinationDirectory, -1)) ? substr($destinationDirectory, 0, -1) : $destinationDirectory;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Sg\Generator
     */
    public function setOuput(Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return void
     */
    public function generate()
    {
        if(null !== $this->output)
        {
            $this->output->writeln("<comment>Starting static site generation.</comment>");
        }

        try
        {
            $this
                ->checkSourceDirectory($this->sourceDirectory)
                ->checkDestinationDirectory($this->destinationDirectory)
                ->checkLayoutFile()
                ->checkPagesDirectory()
                ->process()
            ;
        }
        catch(\Exception $exception)
        {
            $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
        }

        if(null !== $this->output)
        {
            $this->output->writeln("<comment>Static site generation done.</comment>");
        }
    }

    /**
     * @return \Sg\Generator
     */
    public function process()
    {
        // For each fiile in the pages directory we generate a html file

        $finder = new Component\Finder\Finder();
        $files = $finder->files()->name('*.twig')->in($this->pageDirectory);

        $twigLoader = new \Twig_Loader_Filesystem($this->sourceDirectory);
        $twig = new \Twig_Environment($twigLoader, array(
            'cache'         => $this->sourceDirectory . DIRECTORY_SEPARATOR . 'cache',
            'autoescape'    => false
        ));

        foreach($files as $file)
        {
            $template = $twig->loadTemplate('pages' . DIRECTORY_SEPARATOR . $file->getFileName());

            $destinationFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . str_replace(array('pages' . DIRECTORY_SEPARATOR, '.twig'), array('', '.html'), $template->getTemplateName());

            if(false === file_put_contents($destinationFile, $twig->render('layout.twig', array('content' => $template->render(array())))))
            {
                throw new \Exception(sprintf("An error occured while creating file '%s'", $destinationFile));
            }

            $this->writeResult(self::OUTPUT_OK, sprintf('File added : %s', $destinationFile));
        }

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

    /**
     * @param string $result
     * @param string $message
     * @param int $type
     * @return Generator
     */
    private function writeResult($result, $message, $type = 0)
    {
        if(null !== $this->output)
        {
            $writeMessage = sprintf("%s - %s", str_pad($result, 19, ' ', \STR_PAD_RIGHT), $message);
            $this->output->writeln($writeMessage, $type);
        }
    }
}