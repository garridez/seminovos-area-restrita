<?php

namespace AreaRestrita\Middleware\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class MiddlewareGenericFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new $requestedName($container);
    }
}
