<?php

namespace Sg\Processor;

use Symfony\Component;
use Sg\Template\Configuration as TemplateConfiguration;

class Template extends \Sg\Outputter
{
    /** @var bool */
    private $managingAssets = false;

    /**
     * @param Component\Console\Output\OutputInterface $output
     * @param bool $managingAssets
     */
    public function __construct(Component\Console\Output\OutputInterface $output, $managingAssets)
    {
        parent::__construct($output);

        $this->managingAssets = $managingAssets;
    }

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
            $template = new \Sg\Template($this->getOutput());
            $template
                ->setTemplate($twig->loadTemplate('pages' . DIRECTORY_SEPARATOR . $file->getFileName()))
                ->setName($file->getBasename('.' . $file->getExtension()))
            ;

            if(true === $this->managingAssets)
            {
                $template->manageAssets($sourceDirectory, $destinationDirectory);
            }

//            die("FFFFFUUUUUCCCCCKKKKK" . PHP_EOL);

            $destinationFile        = $destinationDirectory . DIRECTORY_SEPARATOR . str_replace(array('pages' . DIRECTORY_SEPARATOR, '.twig'), array('', '.html'), $template->getTemplate()->getTemplateName());
            $destinationFileExists  = is_file($destinationFile);

            $fileContent = $twig->render('layout.twig', array(
                    'content'       => $template->getTemplate()->render(array()),
                    'stylesheets'   => $template->getStylesheets(),
                    'javascript'    => $template->getJavascript()
                )
            );


            if(false === file_put_contents($destinationFile, $fileContent))
            {
                throw new \Exception(sprintf("An error occured while creating file '%s'", $destinationFile));
            }

            $this->writeResult(self::OUTPUT_OK, sprintf('File %s : %s', $destinationFileExists ? 'modified' : 'added', $destinationFile));
        }

        return $this;
    }
}
