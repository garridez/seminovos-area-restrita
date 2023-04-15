<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Event as ApiClientEvents;
use SnBH\ApiClient\Response as ApiClientResponse;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\EventManager\Event as ZendEvent;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\Resources as TranslatorResources;
use Laminas\I18n\Translator\Translator as I18nTranslator;
use Laminas\Log\Logger;
use Laminas\Mvc\I18n\Translator as MvcTranslator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\AbstractValidator;
use Laminas\Session\SessionManager;

class Module
{

    final public const SESSION_NAMESPACE = 'LOGIN_SESSION';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        global $container;
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, $this->onDispatchError(...));
        $container = $sm = $e->getApplication()->getServiceManager();
        $this->setLogger($sm);
        $this->setMeasureApiResponseTime($sm);
        $this->translatorConfig();
        $this->showChat($sm);
        $this->apiUserHeader($sm);
    }

    public function apiUserHeader(ServiceManager $sm)
    {
        /* @var $sessionManager AuthService */
        $authService = $sm->get(AuthService::class);
 

        /** @var ApiClient $apiClient */
        $apiClient = $sm->get(ApiClient::class);
        /* @var $sessionManager SessionManager */
        $sessionManager = $sm->get(SessionManager::class);

        $apiClient->getEventManager()->attach(ApiClientEvents::EVENT_PRE_SEND, function (ZendEvent $event) use ($authService, $sessionManager) {
            $client = $event->getTarget();
            if (!$client instanceof \Laminas\Http\Client) {
                return;
            }
            $identity = $authService->getIdentity();
            if ($identity) {
                $client->getRequest()->getHeaders()->addHeaderLine('Idcadastro', $identity);
                $client->getRequest()->getHeaders()->addHeaderLine('Sessid', $sessionManager->getId());
            }
        });
    }

    public function showChat($sm)
    {
        define('SHOW_CHAT', 1);
        return;
        $cadastro = $sm->get(Model\Cadastros::class)->getCurrent();

        if (!$cadastro) {
            define('SHOW_CHAT', 0);
            return;
        }
        $idCadastrosPermitidos = [
            62, // bonjardim@me.com
            210195, //sara@seminovosbh.com.br
            248584, //felipe@seminovosbh.com.br
            321321, //raul@seminovosbh.com.br  
            327312, //wesley@seminovosbh.com.br
            335671, //joao@seminovosbh.com.br
        ];
        define('SHOW_CHAT', in_array($cadastro['idCadastro'], $idCadastrosPermitidos));
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

        $apiClient->getEventManager()->attach(ApiClientEvents::EVENT_RESPONSE, function (ZendEvent $e) use ($logger) {
            /** @var ApiClientResponse $apiResponse */
            $apiResponse = $e->getTarget();
            if ($apiResponse->getTotalTime() < 1 && $apiResponse->status == 200) {
                return;
            }
            $headTimeApplication = $apiResponse->getHttpResponse()->getHeaders()->get('Time-Application');

            $requestParams = $apiResponse->getRequestParams();
            $timeRequest = $apiResponse->getTotalTime();
            $extras = [
                'timeRequest' => $timeRequest,
                'timeApplication' => false,
                'requestParams' => [
                    'method' => $requestParams->getMethod(),
                    'path' => $requestParams->getPath(),
                    'body' => $requestParams->getBody(),
                    'useCache' => $requestParams->getUseCache(),
                ],
                'requestResponse' => $apiResponse->getBody(),
            ];
            if ($headTimeApplication) {
                $extras['timeApplication'] = (float) $headTimeApplication->getFieldValue();
            }

            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            array_shift($trace); // ignore this clousure;
            array_shift($trace); // ignore Laminas\EventManager\EventManager->tiggerListeners()
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

    /**
     * Configura o translaro para os validadores de formulário
     * É travado em português por motivos óbveis
     */
    public function translatorConfig()
    {
        $I18ntranslator = new I18nTranslator();
        $translator = new MvcTranslator($I18ntranslator);

        $translatorFileResource = sprintf(
            TranslatorResources::getPatternForValidator(),
            TranslatorResources::getBasePath() . 'pt_BR'
        );
        $translator->addTranslationFile(PhpArray::class, $translatorFileResource);

        AbstractValidator::setDefaultTranslator($translator);
    }
}
