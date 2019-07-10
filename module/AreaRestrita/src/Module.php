<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita;

use Zend\Authentication\AuthenticationService as AuthService;
use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{

    const SESSION_NAMESPACE = __CLASS__;

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError']);
        $this->setLogger($e->getApplication()->getServiceManager());
    }

    public function onDispatchError(MvcEvent $e)
    {
        /* @var $authService AuthService */
        $authService = $e->getApplication()->getServiceManager()->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            $e->getViewModel()->setTemplate('layout/blank');
        }
    }

    public function setLogger(ServiceManager $sm)
    {
        $logger = $sm->get('logger');

        Logger::registerErrorHandler($logger, true);
        Logger::registerExceptionHandler($logger);
        Logger::registerFatalErrorShutdownFunction($logger);
    }
}
