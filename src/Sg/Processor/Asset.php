<?php

namespace Sg\Processor;

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

/**
 * Asset processor class.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Asset extends \Sg\Outputter
{
    /** @var string|null */
    protected $source = null;

    /** @var string|null */
    protected $destination = null;

    /**
     * @param string $source
     * @return \Sg\Processor\Asset
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
    /**
     * @param string $destination
     * @return \Sg\Processor\Asset
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     */
    public function process($sourceDirectory, $destinationDirectory)
    {
        // Process stylesheet assets
        $stylesheetSourceDirectory = $sourceDirectory . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'css';

        if(true === is_dir($stylesheetSourceDirectory))
        {
            $stylesheetDestinationDirectory = $destinationDirectory . DIRECTORY_SEPARATOR . 'css';

            $stylesheetFinder   = new \Symfony\Component\Finder\Finder();
            $stylesheets              = $stylesheetFinder->files()->in($stylesheetSourceDirectory);

            /** @var \Symfony\Component\Finder\SplFileInfo $stylesheet */
            foreach($stylesheets as $stylesheet)
            {
                $assetFile = $stylesheetDestinationDirectory . DIRECTORY_SEPARATOR . str_replace('.less', '.css', $stylesheet->getRelativePathname());
                $assetFileExists = is_file($assetFile);

                $asset = new FileAsset($stylesheetSourceDirectory . DIRECTORY_SEPARATOR . $stylesheet->getRelativePathname(), array(new \Assetic\Filter\LessphpFilter()));

                $this->write($assetFile, $asset->dump());
                $this->outputResult(self::OUTPUT_OK, sprintf("Asset file %s : %s", (true === $assetFileExists) ? 'modified' : 'added', $assetFile));
            }
        }

        // Process javascript assets
        $javascriptSourceDirectory = $sourceDirectory . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'js';

        if(true === is_dir($javascriptSourceDirectory))
        {
            $javascriptDestinationDirectory = $destinationDirectory . DIRECTORY_SEPARATOR . 'js';

            $javascriptFinder   = new \Symfony\Component\Finder\Finder();
            $javascripts        = $javascriptFinder->files()->in($javascriptSourceDirectory);

            /** @var \Symfony\Component\Finder\SplFileInfo $javascript */
            foreach($javascripts as $javascript)
            {
                $assetFile = $javascriptDestinationDirectory . DIRECTORY_SEPARATOR . $javascript->getRelativePathname();
                $assetFileExists = is_file($assetFile);

                $asset = new FileAsset($javascriptSourceDirectory . DIRECTORY_SEPARATOR . $javascript->getRelativePathname());

                $this->write($assetFile, $asset->dump());
                $this->outputResult(self::OUTPUT_OK, sprintf("Asset file %s : %s", (true === $assetFileExists) ? 'modified' : 'added', $assetFile));
            }
        }
    }

    /**
     * @param string $path
     * @param string $contents
     * @return \Sg\Processor\Asset
     * @throws \RuntimeException
     */
    public function write($path, $contents)
    {
        if(false === is_dir($directory = dirname($path)) && false === mkdir($directory, 0777, true))
        {
            throw new \RuntimeException(sprintf("Unable to create directory '%s'.", $directory));
        }

        if(false === file_put_contents($path, $contents))
        {
            throw new \RuntimeException(sprintf("Unable to write file '%s'", $path));
        }

        return $this;
    }
}
