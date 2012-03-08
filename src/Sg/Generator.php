<?php

namespace Sg;

use \Symfony\Component;

class Generator
{
    const OUTPUT_OK         = '[<info>OK</info>]';
    const OUTPUT_FAIL       = '[<error>FAIL</error>]';
    const OUTPUT_COMMENT    = '[<comment>FAIL</comment>]';

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
        $finder = new Component\Finder\Finder();

        $templateProcessor = new \Sg\Processor\Template();
        $templateProcessor->process();

        $mediaProcessor = new \Sg\Processor\Media();
        $mediaProcessor->process();

        $this
            ->processTemplates($finder)
            ->processMedia($finder->create())
        ;

        return $this;
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     * @return \Sg\Generator
     * @throws \Exception
     */
    public function processTemplates(Component\Finder\Finder $finder)
    {
        $files = $finder->files()->name('*.twig')->in($this->pageDirectory);

        $twigLoader = new \Twig_Loader_Filesystem($this->sourceDirectory);
        $twig = new \Twig_Environment($twigLoader, array(
            'autoescape'    => false
        ));

        foreach($files as $file)
        {
            $template = $twig->loadTemplate('pages' . DIRECTORY_SEPARATOR . $file->getFileName());

            $destinationFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . str_replace(array('pages' . DIRECTORY_SEPARATOR, '.twig'), array('', '.html'), $template->getTemplateName());
            $destinationFileExists = is_file($destinationFile);

            if(false === file_put_contents($destinationFile, $twig->render('layout.twig', array('content' => $template->render(array())))))
            {
                throw new \Exception(sprintf("An error occured while creating file '%s'", $destinationFile));
            }

            $this->writeResult(self::OUTPUT_OK, sprintf('File %s : %s', $destinationFileExists ? 'modified' : 'added', $destinationFile));
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     * @return \Sg\Generator
     * @throws \Exception
     */
    public function processMedia(Component\Finder\Finder $finder)
    {
        $mediaDirectory = $this->sourceDirectory . DIRECTORY_SEPARATOR . 'media';

        if(false === is_dir($mediaDirectory))
        {
            $this->writeResult(self::OUTPUT_COMMENT, 'No media directory found.');
            return $this;
        }

        $files = $finder->in($mediaDirectory);

        foreach($files as $file)
        {
            if(true === is_dir($file))
            {
                $destinationDirectory = $this->destinationDirectory . DIRECTORY_SEPARATOR . $file->getFileName();

                try
                {
                    $this->copyDirectory($file->getPathName(), $destinationDirectory);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }

                $this->writeResult(self::OUTPUT_OK, sprintf("Directory '%s' added.", $destinationDirectory));
            }
            elseif(true === is_file($file))
            {
                $destinationFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . $file->getPathName();

                try
                {
                    $this->copyFile($file->getPathName(), $destinationFile);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }

                $this->writeResult(self::OUTPUT_OK, sprintf("File '%s' added.", $destinationFile));
            }
            else
            {
                throw new \Exception(sprintf("Unknown type for '%s'.", $file->getPathName()));
            }
        }

        return $this;
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDIrectory
     * @return \Sg\Generator
     */
    public function copyDirectory($sourceDirectory, $destinationDIrectory)
    {
        if(false === is_dir($sourceDirectory))
        {
            throw new \Exception(sprintf("'%s' is not a directory.", $sourceDirectory));
        }

        // Si oui, on l'ouvre
        if($sourceDirectoryResource = opendir($sourceDirectory))
        {
            // On liste les dossiers et fichiers du répertoire source
            while(($file = readdir($sourceDirectoryResource)) !== false)
            {
                // Si le dossier dans lequel on veut coller n'existe pas, on le créé
                if(!is_dir($destinationDIrectory))
                {
                    mkdir($destinationDIrectory, 0777);
                }

                // S'il s'agit d'un dossier, on relance la fonction récursive
                if(is_dir($sourceDirectory . $file) && $file != '..'  && $file != '.')
                {
                    $this->copyDirectory($sourceDirectory.$file . DIRECTORY_SEPARATOR, $destinationDIrectory.$file . DIRECTORY_SEPARATOR);
                }
                // S'il sagit d'un fichier, on le copie simplement
                elseif($file != '..'  && $file != '.')
                {
                    copy($sourceDirectory . $file, $destinationDIrectory . $file);
                }
            }
            // On ferme $dir2copy
            closedir($sourceDirectoryResource);
        }

        return $this;
    }

    public function copyFile($sourceFile, $destinationFile)
    {
        if(false === is_file($sourceFile))
        {
            throw new \Exception(sprintf("'%s' is not a file.", $sourceFile));
        }

        // Effectuer la copie
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