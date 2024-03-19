<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Cache\Storage\Adapter\Redis;

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    //'Laminas\ZendFrameworkBridge',
    'Laminas\Mvc\Middleware',
    'Laminas\Router',
    'Laminas\Session',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Log',
    'Laminas\Form',
    'Laminas\Cache',
    Redis::class,
    Filesystem::class,
    'Laminas\Validator',

    //'TwbBundle',
    'SnBH\ApiClientModule',
    'SnBH\ApiModel',
    'SnBH\Common',
    'AreaRestrita',
    'AreaRestritaAnuncio',
    'SnBH\Integrador',
    'SnBH\Zoop',
    'SnBH\Importer',
];
