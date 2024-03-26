<?php

namespace AreaRestrita\Middleware;

use Laminas\Authentication\AuthenticationService as AuthServ;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Router\Http\RouteMatch;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiModel\Model\Veiculos;

/**
 * Esse middleware verifica se o veículo que está na URL é do usuário que está logado
 * Se não for, redireciona para uma tela de erro
 */
class CheckIdVeiculoMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected AuthServ $authService,
        protected ApiClient $apiClient,
        protected RouteMatch $routeMatch,
        protected ContainerInterface $container
    ) {
        $this->authService = $authService;
        $this->apiClient = $apiClient;
        $this->routeMatch = $routeMatch;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $idVeiculo = (int) $this->routeMatch->getParam('idVeiculo', false);

        // Se não tem idVeiculo no como parametro, então continua para o próximo middleware
        if (!$idVeiculo) {
            return $handler->handle($request);
        }
        // Quando está tentando editar um veículo mas não está logado
        if (!$this->authService->hasIdentity()) {
            $url = $this->container
                ->get('Router')
                ->getRoute('auth')
                ->assemble();
            return new RedirectResponse($url);
        }

        /** @var Veiculos $veiculosModel */
        $veiculosModel = $this->container->get(Veiculos::class);
        if ($veiculosModel->isOwner($idVeiculo)) {
            return $handler->handle($request);
        }
        /**
         * @todo criar uma página melhor pra isso
         */
        die('O veículo não existe!');
    }
}
