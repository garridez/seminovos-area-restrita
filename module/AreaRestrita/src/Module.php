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
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Event as ApiClientEvents;
use Zend\EventManager\Event as ZendEvent;
use SnBH\ApiClient\Response as ApiClientResponse;

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
        $sm = $e->getApplication()->getServiceManager();
        $this->setLogger($sm);
        $this->setMeasureApiResponseTime($sm);
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
        Logger::registerFatalErrorShutdownFunction($logger);
        if (!IS_DEV) {
            Logger::registerExceptionHandler($logger);
        }
    }

    public function setMeasureApiResponseTime(ServiceManager $sm)
    {
        /** @var Logger $logger */
        $logger = $sm->get('logger');
        /** @var ApiClient $apiClient */
        $apiClient = $sm->get(ApiClient::class);

        $apiClient->getEventManager()->attach(ApiClientEvents::EVENT_RESPONSE, function(ZendEvent $e) use($logger) {
            /** @var ApiClientResponse $apiResponse */
            $apiResponse = $e->getTarget();
            $requestParams = $apiResponse->getRequestParams();
            $timeRequest = $apiResponse->getTotalTime();
            $extras = [
                'requestParams' => [
                    'method' => $requestParams->getMethod(),
                    'path' => $requestParams->getPath(),
                    'body' => $requestParams->getBody(),
                    'useCache' => $requestParams->getUseCache(),
                ],
                'timeRequest' => $timeRequest,
            ];
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            array_shift($trace); // ignore this clousure;
            array_shift($trace); // ignore Zend\EventManager\EventManager->tiggerListeners()
            $realCallee = [];
            for (; $traceLine = array_shift($trace);) {
                if (isset($traceLine['class']) && $traceLine['class'] == ApiClient::class && $traceLine['function'] == '__call') {
                    $realCallee[] = array_shift($trace);
                    $realCallee[] = array_shift($trace);
                    $realCallee[] = array_shift($trace);
                    break;
                }
            }
            $extras['realCallee'] = $realCallee;
            if ($apiResponse->getTotalTime() > 1) {
                $logger->info("Resposta lenta da API $timeRequest segundos para '{$requestParams->getMethod()} {$requestParams->getPath()}'", $extras);
            }

            if ($apiResponse->status != 200) {
                $logger->err("API retornou {$apiResponse->status} ao invés de 200 para '{$requestParams->getMethod()} {$requestParams->getPath()}' retornado", $extras);
            }
        });
    }
}
