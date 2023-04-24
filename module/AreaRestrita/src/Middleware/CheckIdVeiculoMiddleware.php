<?php

namespace AreaRestrita\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as ServerRequestI;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiModel\Model\Veiculos;
use Laminas\Authentication\AuthenticationService as AuthServ;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Router\Http\RouteMatch;

/**
 * Esse middleware verifica se o veículo que está na URL é do usuário que está logado
 * Se não for, redireciona para uma tela de erro
 */
class CheckIdVeiculoMiddleware implements MiddlewareInterface
{

    protected $authService;
    protected $apiClient;
    protected $routeMatch;

    public function __construct(AuthServ $authService, ApiClient $apiClient, RouteMatch $routeMatch, protected $container)
    {
        $this->authService = $authService;
        $this->apiClient = $apiClient;
        $this->routeMatch = $routeMatch;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $idVeiculo = (int) $this->routeMatch->getParam('idVeiculo', false);

        // Se não tem idVeiculo no como parametro, então continua para o próximo middleware
        if (!$idVeiculo) {
            return $delegate->handle($request);
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
            return $delegate->handle($request);
        }
        /**
         * @todo criar uma página melhor pra isso
         */
        die('O veículo não existe!');
    }
}
