<?php

namespace AreaRestrita\Middleware\Factory;

use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MiddlewareGenericFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
        return new $requestedName($container);
    }
}
