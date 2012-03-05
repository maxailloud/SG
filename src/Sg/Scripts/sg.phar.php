<?php

require_once __DIR__. '/../../../vendor/.composer/autoload.php';

try
{
    $phar = new \Phar(__DIR__ . '/../../../sg.phar');
    $phar->setMetadata(
        array(
            'version' => Sg\Sg::VERSION,
            'author' => 'Maxime AILLOUD',
            'support' => 'maxime@maximeailloud.fr',
            'description' => 'Static site generator',
            'licence' => 'Do what the fuck you want with it !!!'
        )
    );

    $stub = <<<STUB
<?php
Phar::mapPhar(__DIR__ . '/sg.phar');
require('phar://' . __DIR__ . '/sg.phar/sg');

__HALT_COMPILER();
STUB;
    $phar->setStub($stub);

    $phar->buildFromDirectory(__DIR__  . '/../../..');
}
catch(Exception $exception)
{
    echo "An exception occured when generating the phar package : " . $exception->getMessage();
}