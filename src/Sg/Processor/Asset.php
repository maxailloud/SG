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
     * @return \Sg\Processor\Template\Asset
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
    /**
     * @param string $destination
     * @return \Sg\Processor\Template\Asset
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
        $stylesheetSourceDirectory = $sourceDirectory . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'css';
        $this->checkDirectory($stylesheetSourceDirectory);

        $stylesheetDestinationDirectory = $destinationDirectory . DIRECTORY_SEPARATOR . 'css';
        $this->checkDirectory($stylesheetDestinationDirectory, true);

        $stylesheetGlobAsset = new GlobAsset($stylesheetSourceDirectory . DIRECTORY_SEPARATOR . '*');
        echo "<pre>";
        var_dump($stylesheetGlobAsset->dump());
        echo "</pre>" . PHP_EOL;
        $stylesheetAssetManager = new AssetManager();
        $stylesheetAssetManager->set('css', $stylesheetGlobAsset);

        $stylesheetWriter = new AssetWriter($stylesheetDestinationDirectory);
        $stylesheetWriter->writeManagerAssets($stylesheetAssetManager);

        $this->writeResult(self::OUTPUT_OK, 'Asset file of the site treated.');
    }


    /**
     * @param string $directory
     * @param bool $writable
     * @return \Sg\Processor\Asset
     * @throws \Exception
     */
    public function checkDirectory($directory, $writable = false)
    {
        if(false === is_dir($directory))
        {
            throw new \Exception(sprintf("The directory '%s' doesn't exist.", $directory));
        }

        if(false === is_readable($directory))
        {
            throw new \Exception(sprintf("The directory '%s' cannot be read.", $directory));
        }

        if(true === $writable)
        {
            if(false === is_writable($directory))
            {
                throw new \Exception(sprintf("You don't have the right permission to write into the directory '%s'.", $this->destinationDirectory));
            }
        }

        return $this;
    }
}
