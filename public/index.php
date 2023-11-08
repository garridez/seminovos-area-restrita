<?php

ob_start();

register_shutdown_function(function() {
    // Adiciona no cabeçalho da aplicação o tempo de resposta da aplicação
    if (!headers_sent()) {
        header('X-SnBH-Time-Application:' . round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5));
        header('X-SnBH-Instance-Hostname:' . gethostname());
    }
});
use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

// Composer autoloading
//require 'vendor/autoload.php';
require json_decode(file_get_contents('composer.json'), true)['config']['vendor-dir'].'/autoload.php';

$appConfig = require 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require 'config/development.config.php');
}
/**
 * Disponibiliza globalmente o service manager e a aplicação como atalho
 */
/** @var Laminas\ServiceManager\ServiceManager $container */
global $sm, $container, $application, $logger;

$application = Application::init($appConfig);
$container = $sm = $serviceManager = $application->getServiceManager();
$logger = $sm->get('logger');

if ($_SERVER['REQUEST_URI'] === '/show-configs-0d08b90f7c4c3687a0c22747300af643') {
    echo '<pre>';
    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    echo '<h2>$appConfig;</h2>' . "\n";
    var_dump($appConfig);
    echo '<h2>$serviceManager->get(\'config\');</h2>';
    var_dump($serviceManager->get('config'));
    die;
}

// Run the application!
$application->run();
