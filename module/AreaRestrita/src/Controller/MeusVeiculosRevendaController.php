<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;

class MeusVeiculosRevendaController extends AbstractActionController
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

    public function deleteAction()
    {

        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosFotosModel VeiculosFotos */
        $veiculosFotosModel = $this->getContainer()->get(VeiculosFotos::class);

        // Busca os dados das fotos do veiculo
        $dadosVeiculoFotos = $veiculosFotosModel->get($idVeiculo);

        $listaFotos = array();
        foreach ($dadosVeiculoFotos as $key => $dado) {
            $listaFotos[] = $dado['idFoto'];
        }
        #deletar fotos do servidor
        $retorno = $veiculosFotosModel->delete([
            'listaFotos'=> $listaFotos
        ]);

        #deletar registro da tabela veiculos e anuncios_veiculos

        var_dump($retorno);exit;


    }

    public function ativarAction()
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

    public function inativarAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->put([
            'idVeiculo' => $idVeiculo,
            'idStatus' => 5,
            'clicks' => 0
        ], $idVeiculo);

        var_dump($dadosVeiculos);exit;
    }
}