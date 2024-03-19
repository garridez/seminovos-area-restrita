<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Veiculos;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Model\Planos;

class PainelController extends AbstractActionController
{

    public function indexAction()
    {

        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        $cadastro = $cadastrosModel->getCurrent();
        $idCadastro = $cadastro['idCadastro'];


        /* @var $planosModel Planos */
        $planosModel = $this->getContainer()->get(Planos::class);

        $dadosPlanos = $planosModel->get('revenda');

        $key  = array_search( $cadastro['idPlano'], array_column($dadosPlanos, 'idPlanoRevenda'));
        $valorPlanoRevenda = $dadosPlanos[$key]['valor'] ?? 0;



        // Busca os planos de acordo com o tipo
        $dadosPlanos = $planosModel->get('revenda');

        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        $veiculos = $veiculosModel->getAll(['idCadastro' => $idCadastro]);

        $totalVeiculos = $veiculos['total'];

        $idsVeiculos = [];

        $totalVeiculosAtivos= 0;

        foreach($veiculos['data'] as $veiculo) {
            if(in_array($veiculo['idStatus'], [2, 8, 9])) {
                $totalVeiculosAtivos ++;
            }
            $idsVeiculos[] = $veiculo['idVeiculo'];
        }

        $apiClient = $this->getContainer()->get(ApiClient::class);

        $contadorPorVeiculo = $apiClient->contadorGet(['idVeiculo' => $idsVeiculos])->getData();

        //mescla as informações dos acessos, com os dados dos veículos
        //var_dump($contadorPorVeiculo);die;
        foreach($contadorPorVeiculo as $contador) {
            foreach($veiculos['data'] as  $k => $veiculo) {
                if((isset($veiculo['idVeiculo']) && isset($contador['idVeiculo'])) && $veiculo['idVeiculo'] == $contador['idVeiculo']) {
                    $veiculos['data'][$k]['acesso'] = $contador['acesso'];
                    $veiculos['data'][$k]['contato'] = $contador['contato'];
                    $veiculos['data'][$k]['impressao'] = $contador['impressao'];
                    break;
                }
            }
        }

        return new ViewModel(['totalVeiculos' => $totalVeiculos, 'totalVeiculosAtivos' => $totalVeiculosAtivos, 'veiculos' => $veiculos, 'valorPlanoRevenda' => $valorPlanoRevenda]);

    }

    public function contadorPorMarcaAction()
    {
        $apiClient = $this->getContainer()->get(ApiClient::class);

        $contador = $apiClient->contadorGet(['marca' => true])->getData();

        return new JsonModel([
            'success' => 'SUCCESS',
            'data' =>  $contador,
        ]);
    }

    public function contadorPorModeloAction()
    {
        $apiClient = $this->getContainer()->get(ApiClient::class);

        $contador =  $apiClient->contadorGet(['modelo' => true])->getData();

        return new JsonModel([
            'success' => 'SUCCESS',
            'data' =>  $contador,
        ]);
    }

    public function contadorPorCategoriaAction()
    {
        $apiClient = $this->getContainer()->get(ApiClient::class);

        $contador = $apiClient->contadorGet(['categoria' => true])->getData();

        return new JsonModel([
            'success' => 'SUCCESS',
            'data' =>  $contador,
        ]);
    }

    public function detalheAnuncioAction()
    {
        $idVeiculo = $this->params('idVeiculo');

        $veiculoModel = $this->getContainer()->get(Veiculos::class);

        $veiculo = $veiculoModel->get($idVeiculo);

        $apiClient = $this->getContainer()->get(ApiClient::class);

        $contador = $apiClient->contadorGet(['idVeiculo' => $idVeiculo])->getData();

        $cliques = 0;
        $impressoes = 0;
        $contato = 0;

        foreach($contador as $cnt) {

            $cliques += $cnt['acesso'];
            $impressoes += $cnt['impressao'];
            $contato += $cnt['contato'];
        }

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
                $temp_acoes["excluir"] = true;
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
                $temp_acoes["editar_dados"] = true;
                $temp_acoes["editar_fotos"] = true;
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
                $temp_acoes["excluir"] = true;
                break;
            default:
                $temp_acoes = [
                    "<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
                ];
                break;
        }

        $veiculo['botoes'] = $temp_acoes;
        $veiculo['dataExpiracao'] = '';
        $veiculo['intervaloData'] = '';
        $veiculo['frase'] = $frase;

        return new ViewModel(['veiculo' => $veiculo, 'cliques' => $cliques, 'impressoes' => $impressoes, 'contato' => $contato, 'frase' => $frase, 'contador' => $contador]);
    }

    public function tabelaFipeAction(){

        $params = $this->params()->fromPost();

        $apiClient = $this->getContainer()->get(ApiClient::class);
        $data = $apiClient->versaoGet([
            'idModelo' => $params['modeloCarro'],
            'ano' => $params['ano'],
            'idMarca' => $params['idMarca']
        ])->getData();

        return new JsonModel([
            'success' => '200',
            'data' => $data
        ]);

    }
}
