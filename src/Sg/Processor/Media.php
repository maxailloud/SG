<?php

namespace Sg\Processor;

class Media extends \Sg\Outputter
{
    /**
     * @param $sourceDirectory
     * @param $destinationDirectory
     * @throws \Exception
     * @return \Sg\Processor\Media
     */
    public function process($sourceDirectory, $destinationDirectory)
    {
        $mediaDirectory = $sourceDirectory . DIRECTORY_SEPARATOR . 'media';

        if(false === is_dir($mediaDirectory))
        {
            $this->writeResult(self::OUTPUT_COMMENT, 'No media directory found.');
            return $this;
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->depth(0)->in($mediaDirectory);

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
     * @return \Sg\Processor\Media
     */
    public function copyDirectory($sourceDirectory, $destinationDirectory)
    {
        if(false === is_dir($sourceDirectory))
        {
            throw new \Exception(sprintf("'%s' is not a directory.", $sourceDirectory));
        }

        // Si oui, on l'ouvre
        if($sourceDirectoryResource = opendir($sourceDirectory))
        {
            // On liste les dossiers et fichiers du répertoire source
            while(($file = readdir($sourceDirectoryResource)) !== false)
            {
                // Si le dossier dans lequel on veut coller n'existe pas, on le créé
                if(false === is_dir($destinationDirectory))
                {
                    if(false === mkdir($destinationDirectory, 0777))
                    {
                        throw new \Exception(sprintf("An error occured while adding '%s'.", $destinationDirectory));
                    }

                    $this->writeResult(self::OUTPUT_OK, sprintf("Directory '%s' added.", $destinationDirectory));
                }

                // S'il s'agit d'un dossier, on relance la fonction récursive
                if(is_dir($sourceDirectory . $file) && $file != '..'  && $file != '.')
                {
                    $this->copyDirectory($sourceDirectory . DIRECTORY_SEPARATOR. $file, $destinationDirectory . DIRECTORY_SEPARATOR . $file);
                }
                // S'il sagit d'un fichier, on le copie simplement
                elseif($file != '..'  && $file != '.')
                {
                    $this->copyFile($sourceDirectory . DIRECTORY_SEPARATOR . $file, $destinationDirectory . DIRECTORY_SEPARATOR . $file);
                }
            }
            // On ferme $dir2copy
            closedir($sourceDirectoryResource);
        }

        return $this;
    }

    /**
     * @param string $sourceFile
     * @param string $destinationFile
     * @return \Sg\Processor\Media
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

        $this->writeResult(self::OUTPUT_OK, sprintf('Media file %s : %s', (true === $destinationFileExists) ? 'modified' : 'added', $destinationFile));

        return $this;
    }
}
