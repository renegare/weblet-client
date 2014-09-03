<?php

define('PROJECT_ROOT', realpath(__DIR__.'/../../../'));

$loader = require PROJECT_ROOT . '/vendor/autoload.php';

// add test php classes to the autoloader
$loader->addPsr4('Renegare\\Weblet\\Client\\Test\\', realpath(PROJECT_ROOT . '/test/phpunit'));
