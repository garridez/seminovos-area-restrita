<?php

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// @todo Colocar a versão como variável de ambiente
define('APPLICATION_VERSION', file_get_contents('version'));
define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
define('APPLICATION_PROD', APPLICATION_ENV === 'production');
define('APPLICATION_DEV', !APPLICATION_PROD);

// Composer autoloading
require 'vendor/autoload.php';

$appConfig = require 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require 'config/development.config.php');
}
/**
 * Disponibiliza globalmente o service manager e a aplicação como atalho
 */
/** @var Zend\ServiceManager\ServiceManager $container */
global $sm, $container, $application;

$application = Application::init($appConfig);
$container = $sm = $application->getServiceManager();

// Run the application!
$application->run();
