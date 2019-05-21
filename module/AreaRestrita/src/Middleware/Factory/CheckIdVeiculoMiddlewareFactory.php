<?php

namespace AreaRestrita\Middleware\Factory;

use AreaRestrita\Middleware\CheckIdVeiculoMiddleware;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

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
