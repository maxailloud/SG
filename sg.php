#!/usr/bin/env php
<?php

require_once __DIR__. '/vendor/.composer/autoload.php';

use Sg\Application;
use Sg\Sg;

$console = new Application('SG', Sg::VERSION);
$console->run();
