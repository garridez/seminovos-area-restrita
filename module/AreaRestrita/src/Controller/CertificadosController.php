<?php

namespace AreaRestrita\Controller;

use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;

class CertificadosController extends AbstractActionController
{
    protected ContainerInterface $container;
    protected array $routeParams;

    public function __construct()
    {
        // phpcs:ignore
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /** @var RouteMatch $routeMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction(): ViewModel
    {
        $placa = $this->params('placa');

        $res = $this->getApiClient()->veiculosCertificadosGet([], $placa);

        $dados = $res->getData();

        return new ViewModel([
            'placa' => $placa,
            'dadosCertificados' => $dados,
        ]);
    }
}
