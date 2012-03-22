<?php

namespace Sg\Phar;

class Builder extends \Sg\Outputter
{
    /**
     * @throws \Exception|\RuntimeException
     */
    public function build()
    {
        if (extension_loaded('phar') === false)
        {
            throw new \RuntimeException('Phar extension is mandatory to use this PHAR');
        }

        try
        {
            $phar = new \Phar(__DIR__ . '/../../../sg.phar', 0, 'sg.phar');
            $phar->setMetadata(
                array(
                    'version'       => \Sg\Sg::VERSION,
                    'author'        => 'Maxime AILLOUD',
                    'support'       => 'maxime.ailloud@gmail.com',
                    'description'   => 'Static site generator in PHP',
                    'licence'       => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LICENCE')
                )
            );

            $stubFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'stub.php';
            if(false === is_file($stubFilePath))
            {
                throw new \Exception('Unable to find stub file');
            }

            $phar->setStub(file_get_contents($stubFilePath));

            $baseDirectory = __DIR__ . '/../../..';

            $phar->buildFromIterator($this->getDirectoryIterator($baseDirectory), $baseDirectory);
//            $phar->buildFromDirectory($baseDirectory);

            $this->writeResult(self::OUTPUT_OK, 'PHAR archive generated');
        }
        catch(Exception $exception)
        {
            $this->writeResult(self::OUTPUT_FAIL, sprintf('An error occured when generating the PHAR archive %s'), $exception->getMessage());
        }
    }

    /**
     * @param string $directory
     * @return \Iterator
     */
    private function getDirectoryIterator($directory)
    {
        $fileFinder = new \Symfony\Component\Finder\Finder();
        $fileFinder
//            ->name('LICENCE')
            ->ignoreVCS(true)
            ->exclude('.idea')
            ->notName('sg.phar')
            ->notName('.gitignore')
            ->notName('composer.*')
            ->notName('PharCommand.php')
            ->in($directory)
        ;

        foreach($fileFinder as $file)
        {
            if(true === is_dir($file))
            {
                if('.composer' === $file->getFileName())
                {
                    echo "<pre>";
                    var_dump(realpath($file->getPathname()));
                    echo "</pre>" . PHP_EOL;
                }
                if('vendor' === $file->getFileName())
                {
                    echo "<pre>";
                    var_dump(realpath($file->getPathname()));
                    echo "</pre>" . PHP_EOL;
                }
            }
            if(true === is_file($file))
            {
                if('autoload.php' === $file->getFileName())
                {
                    echo "<pre>";
                    var_dump(realpath($file->getPathname()));
                    echo "</pre>" . PHP_EOL;
                }
            }
        }


        return $fileFinder->getIterator();
    }
}
