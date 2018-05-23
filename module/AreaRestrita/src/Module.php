<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita;

use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService as AuthService;

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
    }

    public function onDispatchError(MvcEvent $e)
    {
        /* @var $authService AuthService */
        $authService = $e->getApplication()->getServiceManager()->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            $e->getViewModel()->setTemplate('layout/blank');
        }
    }
}
