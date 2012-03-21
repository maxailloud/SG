<?php

namespace Sg\Phar;

class Builder extends \Sg\Outputter
{
    /**
     * @throws \runtimeException
     */
    public function build()
    {
        if (extension_loaded('phar') === false)
        {
            throw new \runtimeException('Phar extension is mandatory to use this PHAR');
        }

        try
        {
            $phar = new \Phar(__DIR__ . '/../../../sg.phar');
            $phar->setMetadata(
                array(
                    'version'       => \Sg\Sg::VERSION,
                    'author'        => 'Maxime AILLOUD',
                    'support'       => 'maxime.ailloud@gmail.com',
                    'description'   => 'Static site generator in PHP',
                    'licence'       => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LICENCE')
                )
            );

            $stub = <<<STUB
        <?php
        Phar::mapPhar(__DIR__ . '/sg.phar');
        require('phar://' . __DIR__ . '/sg.phar/sg');

        __HALT_COMPILER();
STUB;
            $phar->setStub($stub);

            //@TODO affiner l'ajout de fichier à l'archive pour ne pas inclure les fichiers git, les fichiers de l'IDE et autre
            $phar->buildFromDirectory(__DIR__  . '/../../..');
            $this->writeResult(self::OUTPUT_OK, 'PHAR archive generated');
        }
        catch(Exception $exception)
        {
            $this->writeResult(self::OUTPUT_FAIL, sprintf('An error occured when generating the PHAR archive %s'), $exception->getMessage());
        }
    }
}
