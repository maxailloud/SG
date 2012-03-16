<?php

namespace Sg\Processor\Template;

use Sg\Processor\Asset as BaseAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;

class Asset extends BaseAsset
{
    private $source = null;
    private $destination = null;

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
     * @param string $templateName
     */
    public function processForTemplate($templateName)
    {
        $this->processStyleSheet($templateName);
        $this->processJavascript($templateName);
    }

    /**
     * @param $sourceDirectory
     * @param $destinationDirectory
     */
    public function processStyleSheet($templateName)
    {
        $css = new AssetCollection(array(
                new GlobAsset($this->source . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '*', array(new LessphpFilter())),
            )
        );

        $stylesheetDirectory = $this->destination . DIRECTORY_SEPARATOR . 'css';

        if(false === is_dir($stylesheetDirectory))
        {
            if(false === mkdir($stylesheetDirectory))
            {
                throw new \Exception(sprintf("Unable to create stylesheet directory '%s'", $stylesheetDirectory));
            }
        }

        $assetFile = $stylesheetDirectory . DIRECTORY_SEPARATOR . $templateName . '.css';

        $assetFileExists = is_file($assetFile);

        if(false === file_put_contents($assetFile, $css->dump()))
        {
            throw new \Exception(sprintf("Unable to create asset file '%s'", $assetFile));
        }
        $this->writeResult(self::OUTPUT_OK, sprintf("Asset file %s : %s", (true === $assetFileExists) ? 'modified' : 'added', $assetFile));
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     */
    public function processJavascript($templateName)
    {
        $js = new AssetCollection(array(
                new GlobAsset($this->source . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . '*'),
            )
        );

        // the code is merged when the asset is dumped
//        echo "<pre>";
//        var_dump($js->dump());
//        echo "</pre>" . PHP_EOL;
//        die("SSSSSTTTTTTOOOOOOPPPPPPP" . PHP_EOL);

        $javascriptDirectory = $this->destination . DIRECTORY_SEPARATOR . 'js';

        if(false === is_dir($javascriptDirectory))
        {
            if(false === mkdir($javascriptDirectory))
            {
                throw new \Exception(sprintf("Unable to create javascript directory '%s'", $javascriptDirectory));
            }
        }

        $assetFile = $javascriptDirectory . DIRECTORY_SEPARATOR . $templateName . '.js';

        $assetFileExists = is_file($assetFile);

        if(false === file_put_contents($assetFile, $js->dump()))
        {
            throw new \Exception(sprintf("Unable to create asset file '%s'", $assetFile));
        }
        $this->writeResult(self::OUTPUT_OK, sprintf("Asset file %s : %s", (true === $assetFileExists) ? 'modified' : 'added', $assetFile));
    }
}
