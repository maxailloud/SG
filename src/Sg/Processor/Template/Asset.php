<?php

namespace Sg\Processor\Template;

use Sg\Processor\Asset as BaseAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;

class Asset extends BaseAsset
{
    /**
     * @param string $templateName
     */
    public function processForTemplate($templateName)
    {
        $assetPath = array(
            $this->processStyleSheet($templateName),
            $this->processJavascript($templateName)
        );

        return $assetPath;
    }

    /**
     * @param string $templateName
     * @return string
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

        return $assetFile;
    }

    /**
     * @param string $templateName
     * @return string
     */
    public function processJavascript($templateName)
    {
        $js = new AssetCollection(array(
                new GlobAsset($this->source . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . '*'),
            )
        );

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

        return $assetFile;
    }
}
