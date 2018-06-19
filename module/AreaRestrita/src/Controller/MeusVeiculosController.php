<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use SnBH\Common\ServiceVeiculo;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;

class MeusVeiculosController extends AbstractActionController
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
        $dadosVeiculos = $veiculosModel->getAll();

        return new ViewModel([
            'meusVeiculos' => $dadosVeiculos
        ]);
    }

    /*
     * Função generica que faz as seguintes ações
     * reativar o veiculo quando for particular
     * renovar o veiculo quando for particular
     * ativar o veiculo quando for revenda
     */

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

        var_dump($dadosVeiculos);
        exit;
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

        var_dump($dadosVeiculos);
        exit;
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

        var_dump($dadosVeiculos);
        exit;
    }

    public function excluirAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        if ($cadastrosModel->isRevenda()) {

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
                'listaFotos' => $listaFotos
            ]);

            #quando o tipoCadastro for 1 (revenda) a API já irá deletar registro das tabelas veiculos, anuncios_veiculos e veiculos_fotos
            $dadosVeiculos = $veiculosModel->delete($idVeiculo);

        } else {

            // Busca os dados do cadastro
            $dadosVeiculos = $veiculosModel->put([
                'idVeiculo' => $idVeiculo,
                'idStatus' => 7,
                'dataRemocao' => date('Y-m-d', strtotime("+1 month"))
            ], $idVeiculo);
        }

        var_dump($dadosVeiculos);
        exit;
    }

    public function veiculoAction()
    {
        $idVeiculo = $this->params('idVeiculo');
        $dadosVeiculo = [];

        $serviceVeiculo = new ServiceVeiculo();

        if ($serviceVeiculo ->verificaCadastroVeiculo($idVeiculo)) {

            /* @var $veiculosModel Veiculos */
            $veiculosModel = $this->getContainer()->get(Veiculos::class);

            // Busca os dados do cadastro
            $dadosVeiculo = $veiculosModel->get($idVeiculo);

        }

        return new ViewModel([
            'veiculo' => $dadosVeiculo
        ]);
    }
}