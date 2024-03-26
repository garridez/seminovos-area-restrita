<?php

namespace AreaRestrita\Middleware\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class MiddlewareGenericFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new $requestedName($container);
    }
}
