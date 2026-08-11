<?php

namespace SnBH\Integrador\Middleware\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\Integrador\Middleware\TokenMiddleware;

class TokenMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);

        return new TokenMiddleware(
            $this->getTokens($container),
            // Fallback sem cache: usado quando o token não está na lista cacheada
            // (ex.: token recém-criado após reativação da loja)
            static fn () => $apiClient->integracaoTokenGet([], null, false)->getData() ?? []
        );
    }

    public function getTokens(ContainerInterface $container)
    {
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        return $apiClient->integracaoTokenGet([], null, true)->getData();
    }
}
