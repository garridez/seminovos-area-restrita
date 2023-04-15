<?php

namespace AreaRestrita\Service\Factory;

use AreaRestrita\Service\Identity;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IdentityFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $identity = $container
            ->get(AuthenticationService::class)
            ->getIdentity();

        $apiClient = $container->get(ApiClient::class);

        return new Identity($identity, $apiClient);
    }
}
