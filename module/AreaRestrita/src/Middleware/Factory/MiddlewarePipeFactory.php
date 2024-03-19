<?php

namespace AreaRestrita\Middleware\Factory;

use AreaRestrita\Middleware;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stratigility\MiddlewarePipe;

class MiddlewarePipeFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
//        var_dump(func_get_args());
//        die;
        $pipe = new MiddlewarePipe();
        foreach (
            [
                Middleware\LoginMiddleware::class,
                Middleware\CheckIdVeiculoMiddleware::class,
                Middleware\DispatchMiddleware::class,
            ] as $middlewareClass
        ) {
            $pipe->pipe($container->get($middlewareClass));
        }

        //die;

        return $pipe;
    }
}
