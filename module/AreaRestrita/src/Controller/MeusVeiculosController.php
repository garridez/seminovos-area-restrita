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

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        if ($cadastrosModel->isRevenda()) {
            $dadosVeiculos = self::retornaValidacaoRevenda($dadosVeiculos);
        } else {
            $dadosVeiculos = self::retornaValidacaoParticular($dadosVeiculos);
        }

        $dadosVeiculos = self::retornaQuantidadePropostasVeiculo($dadosVeiculos);

        $viewModel = new ViewModel([
            'meusVeiculos' => $dadosVeiculos
        ]);

        $request = $this->getRequest();
        // Se for ajax, desativa o layout
        $viewModel->setTerminal($request->isXmlHttpRequest());

        return $viewModel;
    }

    protected function retornaQuantidadePropostasVeiculo($dadosVeiculos)
    {
        /* @var $propostasModel Propostas */
        $propostasModel = $this->getContainer()->get(Propostas::class);
        if(!isset($dadosVeiculos['data'])){
            return;
        }

        foreach ($dadosVeiculos['data'] as $key => $veiculo) {

            $idVeiculo = $veiculo['idVeiculo'];

            // Busca os dados das propostas
            $dadosPropostas = $propostasModel->getAll($idVeiculo, 5 * 60) ?? [];

            $dadosVeiculos['data'][$key]['qdtPropostas'] = count($dadosPropostas);
        }

        return $dadosVeiculos;
    }

    protected function retornaValidacaoRevenda($dadosVeiculos)
    {
        /** Adicionado verificações para cada tipo de plano e status do anuncio */
        foreach ($dadosVeiculos['data'] as $key => $veiculo) {

            $dataAtual = new \DateTime(date('Y-m-d'));

            $dataExpiracao = new \DateTime($veiculo["dataExpiracao"]);
            $intevaloData = $dataAtual->diff($dataExpiracao);
            $intevaloData = (int) $intevaloData->format('%R%a');
            $dataExpiracao = $dataExpiracao->format('d/m/Y');

            $dataCadastro = new \DateTime($veiculo["dataCadastro"]);
            $intervaloDataCadastro = $dataCadastro->diff($dataAtual);
            $intervaloDataCadastro = (int) $intervaloDataCadastro->format('%R%a');

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
                "renovar_plano" => false,
                "inativar" => false,
            ];

            switch ($veiculo['idStatus']) {
                case "1":
                    $frase = "";
                    break;
                case "2":
                    $frase = "Anúncio ativo no site";
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["editar_fotos"] = true;
                    $temp_acoes["excluir"] = true;
                    $temp_acoes["inativar"] = true;
                    $temp_acoes["trocar_plano"] = true;
                    break;
                case "3":
                    $frase = "";
                    break;
                case "4":
                    $frase = "";
                    $temp_acoes["excluir"] = true;
                    $temp_acoes["inativar"] = true;
                    if ($veiculo['idPlano'] == 1) {
                        $temp_acoes["trocar_plano"] = true;
                    }
                    break;
                case "5":
                    $frase = "Anúncio inativo no site";
                    $temp_acoes["reativar"] = true;
                    $temp_acoes["excluir"] = true;
                    break;
                case "6":
                    $frase = "";
                    break;
                case "7":
                    $frase = "";
                    break;
                case "8":
                    $frase = "";
                    break;
                case "9":
                    $frase = "";
                    break;
                case "10":
                    $frase = "";
                    break;
                default:
                    $temp_acoes = [
                        "<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
                    ];
                    break;
            }

            $dadosVeiculos['data'][$key]['botoes'] = $temp_acoes;
            $dadosVeiculos['data'][$key]['dataExpiracao'] = $dataExpiracao;
            $dadosVeiculos['data'][$key]['intervaloData'] = $intevaloData;
            $dadosVeiculos['data'][$key]['frase'] = $frase;
        }

        return $dadosVeiculos;
    }

    protected function retornaValidacaoParticular($dadosVeiculos)
    {
        if (!isset($dadosVeiculos['data'])) {
            return $dadosVeiculos;
        }
        /** Adicionado verificações para cada tipo de plano e status do anuncio */
        foreach ($dadosVeiculos['data'] as $key => $veiculo) {

            $dataAtual = new \DateTime(date('Y-m-d'));

            $dataExpiracao = new \DateTime($veiculo["dataExpiracao"]);
            $intevaloData = $dataAtual->diff($dataExpiracao);
            $intevaloData = (int) $intevaloData->format('%R%a');
            $dataExpiracao = $dataExpiracao->format('d/m/Y');

            $dataCadastro = new \DateTime($veiculo["dataCadastro"]);
            $intervaloDataCadastro = $dataCadastro->diff($dataAtual);
            $intervaloDataCadastro = (int) $intervaloDataCadastro->format('%R%a');

            $dataTrocaStatus = new \DateTime($veiculo["dataTrocaStatus"]);
            $dataAtual->format('Y-m-d H:i:s');
            $dataAtuall = new \DateTime(date('Y-m-d H:i:s'));
            $intervaloDataTrocaStatus = $dataTrocaStatus->diff($dataAtuall);
            $intervaloDataTrocaStatus = (int) $intervaloDataTrocaStatus->format('%R%a');

            /* @var $pagamentosModel Pagamentos */
            $pagamentosModel = $this->getContainer()->get(Pagamentos::class);
            // Busca os dados do pagamento
            $pagamentosVeiculos = $pagamentosModel->get(null, 60);

            $statusPagamento = null;
            $statusPagamento = $this->getVariavelltimoPagamentoVeiculo($pagamentosVeiculos,$veiculo['idVeiculo'],"status");

            $planoPagamento = null;
            $planoPagamento = $this->getVariavelltimoPagamentoVeiculo($pagamentosVeiculos,$veiculo['idVeiculo'],"idPlano");

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
                "renovar_plano" => false,
                "alerta" => false,
            ];
            switch ($veiculo['idStatus']) {
                case "1":
                    $frase = "Aguardando confirmação de pagamento";
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["enviar_comprovante"] = true;
                    $temp_acoes["plano_comprovante"] = $planoPagamento;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                        $temp_acoes["realizar_pagamento"] = true;
                    }
                    break;
                case "2":
                    $frase = "Anúncio ativo no site";
                    $temp_acoes["vendido"] = true;
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["editar_fotos"] = true;
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["upgrade_plano"] = true;
                    }
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["excluir"] = true;
                    }
                    if ($veiculo['idPlano'] != 1 && $intevaloData <= 2){
                        $temp_acoes["reativar"] = true;
                    }
                    if($statusPagamento == 1){
                        $temp_acoes["enviar_comprovante"] = true;
                        $temp_acoes["plano_comprovante"] = $planoPagamento;
                    }
                    break;
                case "3":
                    $frase = "Conclua o cadastro do anúncio";
                    $temp_acoes["editar_dados"] = true;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                    }
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["upgrade_plano"] = true;
                    }
                    break;
                case "4":
                    $frase = "Renove seu anúncio (Os anúncios só podem ser editados após renovação)";
                    $temp_acoes["vendido"] = true;
                    $temp_acoes["excluir"] = true;
                    if ($veiculo['idPlano'] == 4) {
                        $temp_acoes["reativar"] = true;
                        //$temp_acoes["renovar"] = true;
                        $temp_acoes["renovar_plano"] = true;
                    } elseif ($veiculo['idPlano'] == 1) {
                        $temp_acoes["upgrade_plano"] = true;
                    } else {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "5":
                    $frase = "Anúncio inativo no site. Entre em conto com nosso atendimento";
                    $temp_acoes["alerta"] = true;
                    break;
                case "6":
                    $frase = "Aguardando liberação";
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["trocar_plano"] = true;
                    }
                    break;
                case "7":
                    $frase = "";
                    if ($veiculo['idPlano'] != 1 && $intervaloDataTrocaStatus <= 2) {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "8":
                    $frase = "Veículo vendido";
                    if ($veiculo['idPlano'] != 1 && ($intervaloDataTrocaStatus <= 2)) {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "9":
                    $frase = "";
                    $temp_acoes["upgrade_plano"] = true;
                    $temp_acoes["editar_dados"] = true;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                    }
                    break;
                case "10":
                    $frase = "";
                    $temp_acoes["trocar_plano"] = true;
                    $temp_acoes["editar_dados"] = true;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                    }
                    break;
                default:
                    $temp_acoes = [
                        "<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
                    ];
                    break;
            }

            $dadosVeiculos['data'][$key]['botoes'] = $temp_acoes;
            $dadosVeiculos['data'][$key]['dataExpiracao'] = $dataExpiracao;
            $dadosVeiculos['data'][$key]['intervaloData'] = $intevaloData;
            $dadosVeiculos['data'][$key]['frase'] = $frase;
        }

        return $dadosVeiculos;
    }

    /*
     * Verifica qual a ultima entrada de pagamento e captura a variavel solicitada desse
     * @param array $pagamentosVeiculos, int $idVeiculo, string $variavel
     * @return type $result
     */
    protected function getVariavelltimoPagamentoVeiculo($pagamentosVeiculos, $idVeiculo, $variavel)
    {
        if (!isset($pagamentosVeiculos['data'])) {
            return null;
        }

        $result = null;
        $auxData = null;//new \DateTime('1969-01-01');

        foreach ($pagamentosVeiculos['data'] AS $pagamento){
            if($pagamento["idVeiculo"] == $idVeiculo){
                $dataCadastro = new \DateTime($pagamento["dataCadastro"]);

                if($dataCadastro > $auxData){
                    $auxData = $dataCadastro;
                    $result = (int) $pagamento[$variavel];
                }
            }
        }

        return $result;

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

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculo = $veiculosModel->get($idVeiculo);

        /* @var $propostasModel Propostas */
        $propostasModel = $this->getContainer()->get(Propostas::class);

        // Busca os dados das propostas
        $dadosPropostas = $propostasModel->getAll($dadosVeiculo['idVeiculo']) ?? [];

        return new ViewModel([
            'propostas' => $dadosPropostas,
            'veiculo' => $dadosVeiculo
        ]);
    }

    public function qtdAnunciosMenuAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    public function chatAction(){
        $viewModel = new ViewModel();
        return $viewModel;
    }
}
