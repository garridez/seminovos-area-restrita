<?php

namespace SnBH\Integrador\Middleware\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use SnBH\Integrador\Middleware\TokenMiddleware;
use SnBH\ApiClient\Client as ApiClient;

class TokenMiddlewareFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $tokens = $this->getTokens($container);
        return new TokenMiddleware($tokens);
    }

    public function getTokens($container)
    {
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        return $apiClient->integracaoTokenGet([], null, true)->getData();
        
    }
}
