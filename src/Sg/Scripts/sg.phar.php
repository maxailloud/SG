<?php

require_once __DIR__. '/../../../vendor/.composer/autoload.php';

try
{
    $phar = new \Phar(__DIR__ . '/../../../sg.phar');
    $phar->setMetadata(
        array(
            'version'       => Sg\Sg::VERSION,
            'author'        => 'Maxime AILLOUD',
            'support'       => 'maxime.ailloud@gmail.com',
            'description'   => 'Static site generator',
            'licence'       => 'DO WHAT THE FUCK YOU WANT TO !!!'
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
}
catch(Exception $exception)
{
    echo "An exception occured when generating the phar package : " . $exception->getMessage();
}