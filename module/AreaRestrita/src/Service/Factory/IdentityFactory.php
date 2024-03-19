<?php

namespace AreaRestrita\Service\Factory;

use AreaRestrita\Service\Identity;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;

class IdentityFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @return Identity
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $identity = $container
            ->get(AuthenticationService::class)
            ->getIdentity();

        $apiClient = $container->get(ApiClient::class);

        return new Identity($identity, $apiClient);
    }
}
