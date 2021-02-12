<?php

ob_start();

register_shutdown_function(function() {
    // Adiciona no cabeçalho da aplicação o tempo de resposta da aplicação
    if (!headers_sent()) {
        header('X-SnBH-Time-Application:' . round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5));
        header('X-SnBH-Instance-Hostname:' . gethostname());
    }
});
use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// Composer autoloading
require '../vendor/autoload.php';

$appConfig = require 'config/application.config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
