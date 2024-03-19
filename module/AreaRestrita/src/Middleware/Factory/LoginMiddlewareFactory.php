<?php

namespace AreaRestrita\Middleware\Factory;

use AreaRestrita\Middleware\LoginMiddleware;
use interop\container\containerinterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoginMiddlewareFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
        $authService = $container->get(AuthenticationService::class);
        $router = $container->get('Router');

        return new LoginMiddleware($authService, $router);
    }
}
