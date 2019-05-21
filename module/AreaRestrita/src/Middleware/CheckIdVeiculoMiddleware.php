<?php

namespace AreaRestrita\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as ServerRequestI;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiModel\Model\Veiculos;
use Zend\Authentication\AuthenticationService as AuthServ;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Router\Http\RouteMatch;

/**
 * Esse middleware verifica se o veículo que está na URL é do usuário que está logado
 * Se não for, redireciona para uma tela de erro
 */
class CheckIdVeiculoMiddleware implements MiddlewareInterface
{

    protected $authService;
    protected $apiClient;
    protected $routeMatch;
    protected $container;

    public function __construct(AuthServ $authService, ApiClient $apiClient, RouteMatch $routeMatch, $container)
    {
        $this->authService = $authService;
        $this->apiClient = $apiClient;
        $this->routeMatch = $routeMatch;
        $this->container = $container;
    }

    public function process(ServerRequestI $request, DelegateI $delegate)
    {
        $idVeiculo = $this->routeMatch->getParam('idVeiculo', false);

        // Se não tem idVeiculo no como parametro, então continua para o próximo middleware
        if (!$idVeiculo) {
            return $delegate->process($request);
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
            return $delegate->process($request);
        }
        /**
         * @todo criar uma página melhor pra isso
         */
        die('O veículo não existe');
    }
}
