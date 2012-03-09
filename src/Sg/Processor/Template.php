<?php

namespace Sg\Processor;

class Template extends \Sg\Outputter
{
    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @return \Sg\Processor\Template
     * @throws \Exception
     */
    public function process($sourceDirectory, $destinationDirectory)
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $files  = $finder->files()->name('*.twig')->in($sourceDirectory . DIRECTORY_SEPARATOR . 'pages');

        $twigLoader = new \Twig_Loader_Filesystem($sourceDirectory);
        $twig       = new \Twig_Environment($twigLoader, array(
            'autoescape'    => false
        ));

        foreach($files as $file)
        {
            $template = $twig->loadTemplate('pages' . DIRECTORY_SEPARATOR . $file->getFileName());

            $destinationFile        = $destinationDirectory . DIRECTORY_SEPARATOR . str_replace(array('pages' . DIRECTORY_SEPARATOR, '.twig'), array('', '.html'), $template->getTemplateName());
            $destinationFileExists  = is_file($destinationFile);

            if(false === file_put_contents($destinationFile, $twig->render('layout.twig', array('content' => $template->render(array())))))
            {
                throw new \Exception(sprintf("An error occured while creating file '%s'", $destinationFile));
            }

            $this->writeResult(self::OUTPUT_OK, sprintf('File %s : %s', $destinationFileExists ? 'modified' : 'added', $destinationFile));
        }

        return $this;
    }
}
