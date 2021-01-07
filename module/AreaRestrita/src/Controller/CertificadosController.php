<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Service\AuthManager;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use Zend\Authentication\AuthenticationService;

class CertificadosController extends AbstractActionController
{
    protected $container;
    protected $routeParams;
    protected $routeName;

    public function __construct()
    {
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /* @var $routeMatch \Zend\Router\Http\RouteMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction()
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
