<?php

namespace Sg\Processor\Template;

use Sg\Processor\Asset as BaseAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;

/**
 * Asset processor class for Template.
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 */
class Asset extends BaseAsset
{
    /**
     * @param string $templateName
     * @param \Sg\Template\Configuration $templateConfiguration
     * @return array
     */
    public function processForTemplate($templateName, $templateConfiguration)
    {
        $assetPath = array(
            $this->processStyleSheet($templateName, $templateConfiguration->getStylesheets()),
            $this->processJavascript($templateName, $templateConfiguration->getJavascripts())
        );

        return $assetPath;
    }

    /**
     * @param string $templateName
     * @param array $stylesheets
     * @return null|string
     * @throws \Exception
     */
    public function processStyleSheet($templateName, $stylesheets)
    {
        $assetFile = null;

        if(null !== $stylesheets)
        {
            $assets = array();
            foreach($stylesheets as $stylesheet)
            {
                $assets[] = new FileAsset($this->source . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . $stylesheet);
            }

            $css = new AssetCollection($assets);

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

        return $assetFile;
    }

    /**
     * @param string $templateName
     * @param array $javascripts
     * @return null|string
     * @throws \Exception
     */
    public function processJavascript($templateName, $javascripts)
    {
        $assetFile = null;

        if(null !== $javascripts)
        {
            $assets = array();
            foreach($javascripts as $javascript)
            {
                $assets[] = new FileAsset($this->source . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . $javascript);
            }

            $js = new AssetCollection($assets);

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

        return $assetFile;
    }
}
