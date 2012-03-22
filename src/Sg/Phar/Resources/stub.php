<?php

use Sg\Application;
use Sg\Sg;

\Phar::mapPhar('sg.phar');

require_once 'phar://sg.phar/vendor/.composer/autoload.php';


$console = new Application('SG', Sg::VERSION);
$console->run();

__HALT_COMPILER();