<?php

namespace AreaRestrita\Middleware\Factory;

use Laminas\Stratigility\MiddlewarePipe;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use AreaRestrita\Middleware;

class MiddlewarePipeFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
//        var_dump(func_get_args());
//        die;
        $pipe = new MiddlewarePipe();
        foreach ([
        Middleware\LoginMiddleware::class,
        Middleware\CheckIdVeiculoMiddleware::class,
        Middleware\DispatchMiddleware::class,
        ] as $middlewareClass) {
            $pipe->pipe($container->get($middlewareClass));
        }

        //die;

        return $pipe;
    }
}
