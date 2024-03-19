<?php

namespace AreaRestrita\Middleware\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MiddlewareGenericFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new $requestedName($container);
    }
}
