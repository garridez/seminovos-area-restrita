<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Veiculos;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;

class MeusVeiculosParticularController extends AbstractActionController
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
        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(false);

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->get();

        return new ViewModel([
            'meusVeiculos' => $dadosVeiculos
        ]);
    }

    public function excluirAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->put([
            'idVeiculo' => $idVeiculo,
            'idStatus' => 7,
            'dataRemocao' => date('Y-m-d', strtotime("+1 month"))
        ], $idVeiculo);

        var_dump($dadosVeiculos);exit;
    }

    public function vendidoAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->put([
            'idVeiculo' => $idVeiculo,
            'idStatus' => 8,
        ], $idVeiculo);

        var_dump($dadosVeiculos);exit;
    }

    public function reativarAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->put([
            'idVeiculo' => $idVeiculo,
            'idStatus' => 2,
        ], $idVeiculo);

        var_dump($dadosVeiculos);exit;
    }

    public function renovarAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->put([
            'idVeiculo' => $idVeiculo,
            'idStatus' => 2,
        ], $idVeiculo);

        var_dump($dadosVeiculos);exit;
    }

    public function veiculoAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculo = $veiculosModel->getVeiculo([
            'idVeiculo' => $idVeiculo,
            'ignorarCondicoesBasicas' => true
        ]);

        return new ViewModel([
            'veiculo' => $dadosVeiculo
        ]);
    }
}