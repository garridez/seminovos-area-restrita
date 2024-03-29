<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita;

use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\EventManager\Event as ZendEvent;
use Laminas\Http\Client;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\Translator\Loader\PhpArray;
use Laminas\I18n\Translator\Resources as TranslatorResources;
use Laminas\I18n\Translator\Translator as I18nTranslator;
use Laminas\Log\Logger;
use Laminas\Mvc\I18n\Translator as MvcTranslator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\SessionManager;
use Laminas\Session\Validator\RemoteAddr;
use Laminas\Validator\AbstractValidator;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Event as ApiClientEvents;
use SnBH\ApiClient\Response as ApiClientResponse;

class Module
{
    final public const SESSION_NAMESPACE = 'LOGIN_SESSION';

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e): void
    {
        RemoteAddr::setUseProxy(true);
        global $container;
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, $this->onDispatchError(...));
        $container = $sm = $e->getApplication()->getServiceManager();
        $this->setMeasureApiResponseTime($sm);
        $this->translatorConfig();
        $this->showChat($sm);
        $this->apiUserHeader($sm);
    }

    public function apiUserHeader(ServiceManager $sm): void
    {
        /** @var Request */
        $request = $sm->get('Request');
        $path = $request->getUri()->getPath();

        if (preg_match('/^\/(integrador|zoop)/', $path)) {
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }

        /** @var AuthService $sessionManager */
        $authService = $sm->get(AuthService::class);

        /** @var ApiClient $apiClient */
        $apiClient = $sm->get(ApiClient::class);
        /** @var SessionManager $sessionManager */
        $sessionManager = $sm->get(SessionManager::class);

        if (!isset($_COOKIE['TUID'])) {
            $identity = $authService->getIdentity();
            if (!$identity) {
                return;
            }
            $data = $apiClient->crypterPost([
                'data' => $identity,
                'randiv' => true,
            ], null, false)->getData();

            setcookie(
                'TUID',
                $data,
                time() + 60 * 60 * 24 * 30,
                ini_get('session.cookie_path'),
                ini_get('session.cookie_domain'),
                (bool) ini_get('session.cookie_secure'),
                (bool) ini_get('session.cookie_httponly')
            );
        }

        $apiClient->getEventManager()->attach(ApiClientEvents::EVENT_PRE_SEND, function (ZendEvent $event) use ($authService, $sessionManager) {
            $client = $event->getTarget();
            if (!$client instanceof Client) {
                return;
            }
            $identity = $authService->getIdentity();
            if ($identity) {
                $client
                    ->getRequest()
                    ->getHeaders()
                    ->addHeaderLine('Idcadastro', $identity)
                    ->addHeaderLine('Sessid', $sessionManager->getId())
                    ->addHeaderLine('Request-UID', REQUEST_UID)
                    ->addHeaderLine('Request-Origin', 'area-restrita' . ($_SERVER['REQUEST_URI'] ?? ''));
            }
        });
    }

    public function showChat($sm): void
    {
        try {
            $cadastro = $sm->get(Model\Cadastros::class)->getCurrent();
        } catch (\Exception $e) {
            define('SHOW_CHAT', 0);
            return;
        }

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
            81287,
            120854,
            269236,
            290014,
            293083,
            304786,
            317461,
            327800,
            332453,
            335526,
            386468,
            396553,
            436942,
        ];
        define('SHOW_CHAT', in_array($cadastro['idCadastro'], $idCadastrosPermitidos));
    }

    public function onDispatchError(MvcEvent $e): void
    {
        /** @var AuthService $authService */
        $authService = $e->getApplication()->getServiceManager()->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            $e->getViewModel()->setTemplate('layout/blank');
        }
    }

    public function setMeasureApiResponseTime(ServiceManager $sm): void
    {
        return;
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
            $method = $requestParams->getMethod();
            $path = $requestParams->getPath();

            $maxTempoTotal = 1;

            if ($path === '/veiculos-fotos' && ($method === 'POST' || $method === 'DELETE')) {
                $maxTempoTotal = 15;
            }
            if ($path === '/veiculos' && ($method === 'POST' || $method === 'DELETE' || $method === 'PUT')) {
                $maxTempoTotal = 5;
            }
            if ($apiResponse->getTotalTime() > $maxTempoTotal) {
                $logger->info("Resposta lenta da API $timeRequest segundos para '{$method} {$path}'", $extras);
            }

            if ($apiResponse->status != 200 && $apiResponse->status != 405) {
                $logger->err("API retornou {$apiResponse->status} ao invés de 200 para '{$method} {$path}' retornado", $extras);
            }
        });
    }

    /**
     * Configura o translaro para os validadores de formulário
     * É travado em português por motivos óbveis
     */
    public function translatorConfig(): void
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
