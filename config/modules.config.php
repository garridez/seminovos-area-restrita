<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
    'Laminas\\Cache\\Storage\\Adapter\\Redis',
    'Laminas\\Cache\\Storage\\Adapter\\Filesystem',
    'Laminas\Validator',
    
    //'TwbBundle',
    'SnBH\ApiClientModule',
    'SnBH\ApiModel',
    'SnBH\Common',
    'AreaRestrita',
    'AreaRestritaAnuncio',
    'SnBH\Integrador',
    'SnBH\GalaxPay',
    'SnBH\Zoop',
];
