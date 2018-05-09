<?php

namespace AreaRestrita\Service;

use AreaRestrita\Module;
use AreaRestrita\Service\AuthAdapter;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthenticationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container,
        $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage(Module::SESSION_NAMESPACE, 'idCadastro', $sessionManager);
        $authAdapter = new AuthAdapter($container->get(ApiClient::class));

        return new AuthenticationService($authStorage, $authAdapter);
    }
}
