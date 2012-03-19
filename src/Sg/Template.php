<?php

namespace Sg;

use Symfony\Component;

class Template extends \Sg\Outputter
{
    const CONFIGURATION_SEPARATOR   = '===';
    const INCLUDE_STYLESHEET        = '<link href="%s" rel="stylesheet">';
    const INCLUDE_JAVASCRIPT        = '<script src="%s"></script>';

    /** @var \Twig_Environment */
    private $twig = null;

    /** @var \Symfony\Component\Finder\SplFileInfo */
    private $file = null;

    /** @var string */
    private $stylesheetPath = null;

    /** @var string */
    private $javascriptPath = null;

    /** @var boolean */
    private $managingAssets = false;

    /** @var \Sg\Template\Configuration */
    private $configuration = null;

    /** @var string */
    private $content = null;

    /** @var string */
    private $sourceDirectory = null;

    /** @var string */
    private $destinationDirectory = null;

    /**
     * @param \Twig_Environment $twig
     * @return \Sg\Template
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * @return \Twig_Environment
     * @throws \Exception
     */
    public function getTwig()
    {
        if(null === $this->twig)
        {
            throw new \Exception('No twig loader defined');
        }

        return $this->twig;
    }

    /**
     * @param string $name
     * @return \Sg\Template
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getName()
    {
        if(null === $this->file)
        {
            throw new \Exception('No file defined for the template, unable to find name');
        }

        return $this->file->getBasename('.' . $this->file->getExtension());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getFileName()
    {
        if(null === $this->file)
        {
            throw new \Exception('No file defined for the template, unable to find file name');
        }

        return $this->file->getFileName();
    }

    /**
     * @return null|string
     */
    public function getStylesheetPath()
    {
        return sprintf(self::INCLUDE_STYLESHEET, $this->stylesheetPath);
    }

    /**
     * @return null|string
     */
    public function getJavascriptPath()
    {
        return sprintf(self::INCLUDE_JAVASCRIPT, $this->javascriptPath);
    }

    /**
     * @param boolean $managingAssets
     * @return \Sg\Template
     */
    public function setManagingAssets($managingAssets)
    {
        $this->managingAssets = $managingAssets;

        return $this;
    }

    /**
     * @param string $content
     * @return \Sg\Template
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $destinationDirectory
     * @return \Sg\Template
     */
    public function setDestinationDirectory($destinationDirectory)
    {
        $this->destinationDirectory = $destinationDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationDirectory()
    {
        return $this->destinationDirectory;
    }

    /**
     * @param string $sourceDirectory
     * @return \Sg\Template
     */
    public function setSourceDirectory($sourceDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->sourceDirectory;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param \Sg\Template\Configuration $configuration
     * @return \Sg\Template
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return Template
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return void
     */
    public function init()
    {
        $templateContent = file_get_contents($this->file->getPathname());
        $configurationSeparatorPosition = strpos($templateContent, self::CONFIGURATION_SEPARATOR) + strlen(self::CONFIGURATION_SEPARATOR);
        $this->content = substr($templateContent, $configurationSeparatorPosition);

        $this->configuration = new Template\Configuration(substr($templateContent, 0, $configurationSeparatorPosition - strlen(self::CONFIGURATION_SEPARATOR)));
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @return \Sg\Template
     */
    public function manageAssets($sourceDirectory, $destinationDirectory)
    {
        $twig = $this->getTwig();

        $twigTemplate = $this->twig->loadTemplate('pages' . DIRECTORY_SEPARATOR . $this->getFileName());
        $templateRender = $twigTemplate->render(array());

        // Asset processor for the template
        $assetProcessor = new Processor\Template\Asset($this->getOutput());
        $assetProcessor
            ->setSource($sourceDirectory)
            ->setDestination($destinationDirectory)
        ;

        list($this->stylesheetPath, $this->javascriptPath) = $assetProcessor->processForTemplate($this->getName());

        return $this;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function render()
    {
        $this->init();

        if(true === $this->managingAssets)
        {
            $this->manageAssets($this->sourceDirectory, $this->destinationDirectory);
        }

        $destinationFile       = $this->destinationDirectory . DIRECTORY_SEPARATOR . $this->getName() . '.html';
        $destinationFileExists = is_file($destinationFile);

        $fileContent = $this->twig->render('layout.twig', array(
                'content'       => $this->getContent(),
                'stylesheets'   => $this->getStylesheetPath(),
                'javascript'    => $this->getJavascriptPath()
            )
        );

        if(false === file_put_contents($destinationFile, $fileContent))
        {
            throw new \Exception(sprintf("An error occured while creating file '%s'", $destinationFile));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('File %s : %s', $destinationFileExists ? 'modified' : 'added', $destinationFile));
    }
}
