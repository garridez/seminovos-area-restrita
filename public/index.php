<?php

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// Composer autoloading
require 'vendor/autoload.php';

$appConfig = require 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require 'config/development.config.php');
}
/**
 * Disponibiliza globalmente o service manager e a aplicação como atalho
 */
global $sm, $container, $application;

$application = Application::init($appConfig);
$container = $sm = $application->getServiceManager();

// Run the application!
$application->run();
