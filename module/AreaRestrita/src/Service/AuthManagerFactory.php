<?php

namespace AreaRestrita\Service;

use AreaRestrita\Service\AuthManager;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;

class AuthManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authenticationService = $container->get(AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);

        $apiClient = $container->get(ApiClient::class);

        return new AuthManager($authenticationService, $sessionManager, $apiClient);
    }
}