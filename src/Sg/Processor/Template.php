<?php

namespace Sg\Processor;

use Symfony\Component;
use Sg\Template\Configuration as TemplateConfiguration;

/**
 * Template processor class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Template extends \Sg\Outputter
{
    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @param bool $managingAssets
     * @return \Sg\Processor\Template
     * @throws \Exception
     */
    public function process($sourceDirectory, $destinationDirectory, $managingAssets = false)
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $files  = $finder->files()->name('*.twig')->in($sourceDirectory . DIRECTORY_SEPARATOR . 'pages');
        $twig   = new \Twig_Environment(new \Twig_Loader_Filesystem($sourceDirectory), array(
            'autoescape' => false
        ));
        foreach($files as $file)
        {
            $template = new \Sg\Template($this->getOutput());
            $template
                ->setTwig($twig)
                ->setFile($file)
                ->setManagingAssets($managingAssets)
                ->setSourceDirectory($sourceDirectory)
                ->setDestinationDirectory($destinationDirectory)
                ->render()
            ;
        }

        return $this;
    }
}
