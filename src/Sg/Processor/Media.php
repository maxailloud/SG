<?php

namespace Sg\Processor;

class Media
{
    public function process()
    {
        $mediaDirectory = $this->sourceDirectory . DIRECTORY_SEPARATOR . 'media';

        if(false === is_dir($mediaDirectory))
        {
            $this->writeResult(self::OUTPUT_COMMENT, 'No media directory found.');
            return $this;
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->in($mediaDirectory);

        foreach($files as $file)
        {
            if(true === is_dir($file))
            {
                $destinationDirectory = $this->destinationDirectory . DIRECTORY_SEPARATOR . $file->getFileName();

                try
                {
                    $this->copyDirectory($file->getPathName(), $destinationDirectory);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }

                $this->writeResult(self::OUTPUT_OK, sprintf("Directory '%s' added.", $destinationDirectory));
            }
            elseif(true === is_file($file))
            {
                $destinationFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . $file->getPathName();

                try
                {
                    $this->copyFile($file->getPathName(), $destinationFile);
                }
                catch(\Exception $exception)
                {
                    $this->writeResult(self::OUTPUT_FAIL, $exception->getMessage());
                }

                $this->writeResult(self::OUTPUT_OK, sprintf("File '%s' added.", $destinationFile));
            }
            else
            {
                throw new \Exception(sprintf("Unknown type for '%s'.", $file->getPathName()));
            }
        }

        return $this;
    }
}
