<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Propostas;
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
        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->getAll();

        /** Adicionado verificações para cada tipo de plano e status do anuncio */
        foreach ($dadosVeiculos['data'] as $key => $veiculo) {
            $frase = "";
            $temp_acoes = [
                "realizar_pagamento" => false,
                "editar_dados" => false,
                "editar_fotos" => false,
                "vendido" => false,
                "upgrade_plano" => false,
                "excluir" => false,
                "renovar" => false,
                "trocar_plano" => false,
                "reativar" => false,
                "enviar_comprovante" => false,
            ];
            switch ($veiculo['idStatus']) {
            case "1":
                $frase = "Aguardando confirmação de pagamento";
                $temp_acoes["realizar_pagamento"] = true;
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
                $temp_acoes["enviar_comprovante"] = true;
                break;
            case "2":
                $frase = "Anúncio ativo no site";            
                $temp_acoes["vendido"] = true;
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
                if($veiculo['idPlano'] != 4){
                    $temp_acoes["upgrade_plano"] = true;   
                }
                if($veiculo['idPlano'] != 5){
                   $temp_acoes["excluir"] = true;
                } 
                break;
            case "3":
                $frase = "Conclua o cadastro do anúncio";            
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
                if($veiculo['idPlano'] != 4){
                    $temp_acoes["upgrade_plano"] = true;   
                }
                break;
            case "4":
                $frase = "Renove seu anúncio(Os anúncios só podem ser editados após renovação)";
                $temp_acoes["vendido"] = true;
                $temp_acoes["excluir"] = true;
                if($veiculo['idPlano'] == 4){
                    $temp_acoes["reativar"] = true;
                    $temp_acoes["renovar"] = true;
                } elseif ($veiculo['idPlano'] == 1){
                    $temp_acoes["renovar"] = true;
                }else{
                    $temp_acoes["reativar"] = true;
                }
                break;
            case "5":
                $frase = "Anúncio inativo no site";
                // NÃO HÁ AÇÕES PARA ESTE CASO
                break; 
            case "6":
                $frase = "Aguardando liberação";
                $temp_acoes["trocar_plano"] = true;
                break;
            case "7":
                $frase = "";
                if($veiculo['idPlano'] != 5){
                    $temp_acoes["reativar"] = true;
                } 
                break;
            case "8": 
                $frase = "Veículo vendido";                
                if($veiculo['idPlano'] != 5  /* < 48 HORAS MARCADO COMO VENDIDO */){
                    $temp_acoes["reativar"] = true;                    
                } 
                break;
            case "9":
                $frase = "";
                $temp_acoes["trocar_plano"] = true;
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
                 break;
            case "10": 
                $frase = "";
                $temp_acoes["trocar_plano"] = true;
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
               break;
            default: 
                $temp_acoes = [
                    "<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
                ];
                break;
            }

            $dataAtual = new \DateTime(date('Y-m-d'));
            $dataExpiracao = new \DateTime($veiculo["dataExpiracao"]);
            $intevaloData = $dataAtual->diff($dataExpiracao);
            $intevaloData = (int)$intevaloData->format('%R%a');
            $dataExpiracao = $dataExpiracao->format('d/m/Y');

           $dadosVeiculos['data'][$key]['botoes'] = $temp_acoes;
           $dadosVeiculos['data'][$key]['dataExpiracao'] = $dataExpiracao;
           $dadosVeiculos['data'][$key]['intervaloData'] = $intevaloData;
           $dadosVeiculos['data'][$key]['frase'] = $frase;
        }
        $viewModel = new ViewModel([
            'meusVeiculos' => $dadosVeiculos
        ]);
        
        $request = $this->getRequest();
        // Se for ajax, desativa o layout
        $viewModel->setTerminal($request->isXmlHttpRequest());

        return $viewModel;
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

        echo json_encode($dadosVeiculos);
        die;
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

        echo json_encode($dadosVeiculos);
        die;
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
        echo json_encode($dadosVeiculos);
        die;
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
        echo json_encode($dadosVeiculos);
        die;
    }

    public function veiculoAction()
    {
        $idVeiculo = $this->params('idVeiculo');
        $dadosVeiculo = [];

        $serviceVeiculo = new ServiceVeiculo();

        if ($serviceVeiculo->verificaCadastroVeiculo($idVeiculo)) {

            /* @var $veiculosModel Veiculos */
            $veiculosModel = $this->getContainer()->get(Veiculos::class);

            // Busca os dados do cadastro
            $dadosVeiculo = $veiculosModel->get($idVeiculo);
        }

        return new ViewModel([
            'veiculo' => $dadosVeiculo
        ]);
    }

    public function propostasAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        /* @var $propostasModel Propostas */
        $propostasModel = $this->getContainer()->get(Propostas::class);

        // Busca os dados das propostas
        $dadosPropostas = $propostasModel->getAll($idVeiculo);

        $serviceVeiculo = new ServiceVeiculo();

        if ($serviceVeiculo->verificaCadastroVeiculo($idVeiculo)) {

            /* @var $veiculosModel Veiculos */
            $veiculosModel = $this->getContainer()->get(Veiculos::class);

            // Busca os dados do cadastro
            $dadosVeiculo = $veiculosModel->get($idVeiculo);
        }

        // Verifica se retornou propostas para o veículo
        if ($dadosPropostas['status'] == 405) {
            $dadosPropostas = [];
        }

        return new ViewModel([
            'propostas' => $dadosPropostas,
            'veiculo' => $dadosVeiculo
        ]);
    }
}
