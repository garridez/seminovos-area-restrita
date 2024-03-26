<?php

namespace AreaRestrita\Middleware\Factory;

use AreaRestrita\Middleware\LoginMiddleware;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class LoginMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $router = $container->get('Router');

        return new LoginMiddleware($authService, $router);
    }
}
