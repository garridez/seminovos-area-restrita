<?php

namespace AreaRestrita\Middleware\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use AreaRestrita\Middleware\LoginMiddleware;

class LoginMiddlewareFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $router = $container->get('Router');

        return new LoginMiddleware($authService, $router);
    }
}
