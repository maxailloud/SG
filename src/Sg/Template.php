<?php

namespace Sg;

use Symfony\Component;

class Template extends \Sg\Outputter
{
    const CONFIGURATION_SEPARATOR   = '===';
    const INCLUDE_STYLESHEET        = '<link href="%s" rel="stylesheet">';

    /** @var \Twig_TemplateInterface */
    private $template;

    /** @var string */
    private $name;

    /**
     * @param \Twig_TemplateInterface $template
     * @return \Sg\Template
     */
    public function setTemplate(\Twig_TemplateInterface $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return \Twig_TemplateInterface
     * @throws \Exception
     */
    public function getTemplate()
    {
        if(null === $this->template)
        {
            throw new \Exception('No template defined');
        }

        return $this->template;
    }

    /**
     * @param $name
     * @return \Sg\Template
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        if(null === $this->name)
        {
            throw new \Exception('No name for the template defined');
        }

        return $this->name;
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @return \Sg\Template
     */
    public function manageAssets($sourceDirectory, $destinationDirectory)
    {
        $templateRender = $this->getTemplate()->render(array());

        $configurationSeparatorPosition = strpos($templateRender, self::CONFIGURATION_SEPARATOR);
        $templateConfiguration = new Template\Configuration(substr($templateRender, 0, $configurationSeparatorPosition));

        // Asset processor for the template
        $assetProcessor = new Processor\Template\Asset($this->getOutput());
        $assetProcessor
            ->setSource($sourceDirectory)
            ->setDestination($destinationDirectory)
        ;
        $css = $assetProcessor->processForTemplate($this->getName());

//        die("SSSSSTTTTTTOOOOOOPPPPPPP" . PHP_EOL);

        return $this;
    }

    public function getStylesheets()
    {

    }

    public function getJavascript()
    {

    }
}
