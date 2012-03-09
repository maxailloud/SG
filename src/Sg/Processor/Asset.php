<?php

namespace Sg\Processor;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class Asset extends \Sg\Outputter
{
    /** @var \Symfony\Component\Finder\Finder */
    private $finder = null;

    /**
     * @param $sourceDirectory
     * @param $destinationDirectory
     * @throws \Exception
     * @return \Sg\Processor\Asset
     */
    public function process($sourceDirectory, $destinationDirectory)
    {
        $js = new AssetCollection(array(
            new GlobAsset($sourceDirectory . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . '*'),
            new FileAsset($sourceDirectory . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'script.js'),
        ));

//         the code is merged when the asset is dumped
        echo "<pre>";
        var_dump($js->dump());
        echo "</pre>" . PHP_EOL;
        die("SSSSSTTTTTTOOOOOOPPPPPPP" . PHP_EOL);

        $assetDirectory = $sourceDirectory . DIRECTORY_SEPARATOR . 'asset';

        if(false === is_dir($assetDirectory))
        {
            $this->writeResult(self::OUTPUT_COMMENT, 'No asset directory found.');
            return $this;
        }

        $this->finder   = new \Symfony\Component\Finder\Finder();
        $files          = $this->finder->depth(0)->in($assetDirectory);

        foreach($files as $file)
        {
            if(true === is_dir($file))
            {
                $directory = $destinationDirectory . DIRECTORY_SEPARATOR . $file->getFileName();

                try
                {
                    $this->copyDirectory($file->getPathName(), $directory);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }
            }
            elseif(true === is_file($file))
            {
                $destinationFile = $destinationDirectory . DIRECTORY_SEPARATOR . $file->getRelativePathName();

                try
                {
                    $this->copyFile($file->getPathName(), $destinationFile);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }
            }
            else
            {
                throw new \Exception(sprintf("Unknown type for '%s'.", $file->getPathName()));
            }
        }

        return $this;
    }

    /**
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @throws \Exception
     * @return \Sg\Processor\Asset
     */
    public function copyDirectory($sourceDirectory, $destinationDirectory)
    {
        if(false === is_dir($sourceDirectory))
        {
            throw new \Exception(sprintf("'%s' is not a directory.", $sourceDirectory));
        }

        if(false === is_dir($destinationDirectory))
        {
            if(false === mkdir($destinationDirectory, 0777))
            {
                throw new \Exception(sprintf("An error occured while adding '%s'.", $destinationDirectory));
            }
            $this->writeResult(self::OUTPUT_OK, sprintf("Directory '%s' added.", $destinationDirectory));
        }

        $finder = $this->finder->create();
        $files  = $finder->depth(0)->in($sourceDirectory);

        /** @var $file \Symfony\Component\Finder\SplFileInfo */
        foreach($files as $file)
        {
            if(true === is_dir($file))
            {
                $fileRelativePathname   = $file->getRelativePathname();
                $source                 = $sourceDirectory . DIRECTORY_SEPARATOR . $fileRelativePathname;
                $destination            = $destinationDirectory . DIRECTORY_SEPARATOR . $fileRelativePathname;

                $this->copyDirectory($source, $destination);
            }
            elseif(true === is_file($file))
            {
                $fileName           = $file->getBasename();
                $sourceFile         = $sourceDirectory . DIRECTORY_SEPARATOR . $fileName;
                $destinationFile    = $destinationDirectory . DIRECTORY_SEPARATOR . $fileName;

                $this->copyFile($sourceFile, $destinationFile);
            }
            else
            {
                throw new \Exception(sprintf("Unknown type for '%s'.", $file->getPathName()));
            }
        }

        return $this;
    }

    /**
     * @param string $sourceFile
     * @param string $destinationFile
     * @return \Sg\Processor\Asset
     * @throws \Exception
     */
    public function copyFile($sourceFile, $destinationFile)
    {
        if(false === is_file($sourceFile))
        {
            throw new \Exception(sprintf("'%s' is not a file.", $sourceFile));
        }

        $destinationFileExists = is_file($destinationFile);

        if(false === copy($sourceFile, $destinationFile))
        {
            throw new \Exception(sprintf("An error occured while %s '%s'.", (true === $destinationFileExists) ? 'modifying' : 'adding', $destinationFile));
        }

        $this->writeResult(self::OUTPUT_OK, sprintf('Asset file %s : %s', (true === $destinationFileExists) ? 'modified' : 'added', $destinationFile));

        return $this;
    }
}
