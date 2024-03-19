<?php

namespace AreaRestrita\Middleware\Factory;

use AreaRestrita\Middleware\CheckIdVeiculoMiddleware;
use Psr\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SnBH\ApiClient\Client as ApiClient;

class CheckIdVeiculoMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $apiClient = $container->get(ApiClient::class);
        $routeMatch = $container->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        return new CheckIdVeiculoMiddleware($authService, $apiClient, $routeMatch, $container);
    }
}
