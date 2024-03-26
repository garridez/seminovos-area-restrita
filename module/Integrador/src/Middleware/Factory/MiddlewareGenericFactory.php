<?php

namespace SnBH\Integrador\Middleware\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class MiddlewareGenericFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new $requestedName($container);
    }
}
