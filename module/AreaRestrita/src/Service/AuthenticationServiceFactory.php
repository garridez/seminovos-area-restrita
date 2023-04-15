<?php

namespace AreaRestrita\Service;

use AreaRestrita\Module;
use AreaRestrita\Service\AuthAdapter;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;

class AuthenticationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container,
        $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);

        $authStorage = $this->getSessionStorage($sessionManager);

        $authAdapter = new AuthAdapter($container->get(ApiClient::class));

        return new AuthenticationService($authStorage, $authAdapter);
    }

    protected function getSessionStorage(SessionManager $sessionManager)
    {
        try {
            return new SessionStorage(Module::SESSION_NAMESPACE, 'idCadastro', $sessionManager);
        } catch (\Exception $ex) {
            $sessionManager->destroy();
            throw $ex;
        }
    }
}
