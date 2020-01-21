<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Contador;
use AreaRestrita\Model\Veiculos;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use SnBH\ApiClient\Client as ApiClient;

abstract class GranularidadeContator {
    const Dia = 'DATE(time)';
    const Semana = 'WEEK(DATE(time))';
    const Mes = 'MONTH(DATE(time))';
    const Ano = 'YEAR(DATE(time))';
}

abstract class TabelasContador{
    const Acesso = 'acesso';
    const Impressao = 'impressao';
    const Contato = 'contato';
}

class PainelController extends AbstractActionController
{
    private $contador;

    public function indexAction()
    {

        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        $cadastro = $cadastrosModel->getCurrent();
        $idCadastro = $cadastro['idCadastro']; 
        
        $veiculosModel = $this->getContainer()->get(Veiculos::class); 

        $veiculos = $veiculosModel->getAll(compact('idCadastro'));
        
        $totalVeiculos = $veiculos['total'];

        $idsVeiculos = [];

        $totalVeiculosAtivos= 0;

        foreach($veiculos['data'] as $veiculo) {
            if(in_array($veiculo['idStatus'], [2, 8, 9])) {
                $totalVeiculosAtivos ++;
            }
            $idsVeiculos[] = $veiculo['idVeiculo'];
        }

        $this->contador = new Contador();
        $this->contador->gerarQueryAcesso(TabelasContador::Acesso, ['idVeiculo'], $idCadastro, null, $idsVeiculos);

        $contadorPorVeiculo = $this->contador->getDados();

        //mescla as informações dos acessos, com os dados dos veículos
        //var_dump($contadorPorVeiculo);die;
        foreach($contadorPorVeiculo as $contador) {
            foreach($veiculos['data'] as  $k => $veiculo) {
                if($veiculo['idVeiculo'] == $contador['idVeiculo']) {
                    $veiculos['data'][$k]['acesso'] = $contador['contador'];
                    break;
                }
            }
        }

        $this->contador->gerarQueryAcesso(TabelasContador::Impressao, ['idVeiculo'], $idCadastro, null, $idsVeiculos);

        $contadorPorVeiculo = $this->contador->getDados();

        //mescla as informações dos contadores, com os dados dos veículos
        //var_dump($contadorPorVeiculo);die;
        foreach($contadorPorVeiculo as $contador) {
            foreach($veiculos['data'] as  $k => $veiculo) {
                if($veiculo['idVeiculo'] == $contador['idVeiculo']) {
                    $veiculos['data'][$k]['impressao'] = $contador['contador'];
                    break;
                }
            }
        }

        $this->contador->gerarQueryAcesso(TabelasContador::Contato, ['idVeiculo'], $idCadastro, null, $idsVeiculos);

        $contadorPorVeiculo = $this->contador->getDados();

        //mescla as informações dos contadores, com os dados dos veículos
        //var_dump($contadorPorVeiculo);die;
        foreach($contadorPorVeiculo as $contador) {
            foreach($veiculos['data'] as  $k => $veiculo) {
                if($veiculo['idVeiculo'] == $contador['idVeiculo']) {
                    $veiculos['data'][$k]['contato'] = $contador['contador'];
                    break;
                }
            }
        }

        return new ViewModel(compact('totalVeiculos', 'totalVeiculosAtivos', 'veiculos'));

    }

    public function contadorPorMarcaAction()
    {
        return $this->contadorPor(['marca']);
    }

    public function contadorPorModeloAction()
    {
        return $this->contadorPor(['modelo']);
    }

    public function contadorPorCategoriaAction()
    {
        return $this->contadorPor(['categoria']);
    }

    private function contadorPor($campos, $granularidade = GranularidadeContator::Mes)
    {
        try{
            $this->contador =  new Contador();
            $this->contador->gerarQueryAcesso(TabelasContador::Acesso, $campos, null, $granularidade, null, 'contador', 5);
            $contador =  $this->contador->getDados();    
            
            return new JsonModel([
                'success' => 'SUCCESS',
                'data' =>  $contador,
            ]);

        }catch(\Error $e) {

            return new JsonModel([
                'success' => '500',
                'data' =>  $e->getMessage(),
            ]);
        }
    }

    public function detalheAnuncioAction()
    {
        $idVeiculo = $this->params('idVeiculo');
        
        $veiculoModel = $this->getContainer()->get(Veiculos::class);

        $veiculo = $veiculoModel->get($idVeiculo);

        $contador = new Contador();

        $contador->gerarQueryAcesso(TabelasContador::Acesso, ['idVeiculo'], $veiculo['idCadastro'], null, [$idVeiculo]);

        $cliques = $contador->getDados();

        $cliques = $cliques[1]['contador'] ?? 0;  
        

        $contador->gerarQueryAcesso(TabelasContador::Impressao, ['idVeiculo'], $veiculo['idCadastro'], null, [$idVeiculo]);

        $impressoes = $contador->getDados();

        $impressoes = $impressoes[1]['contador'] ?? 0;
        
        
        $contador->gerarQueryAcesso(TabelasContador::Contato, ['idVeiculo'], $veiculo['idCadastro'], null, [$idVeiculo]);

        $contato = $contador->getDados();

        $contato = $contato[1]['contador'] ?? 0;

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


        return new ViewModel(compact('veiculo', 'cliques', 'impressoes', 'contato', 'frase'));        
    }

    public function graficoContagemDiariaAction()
    {
        $tipo = $this->params('tipo');
        
        $idVeiculo = $this->params('idVeiculo');

        $veiculoModel = $this->getContainer()->get(Veiculos::class);

        $veiculo = $veiculoModel->get($idVeiculo);
        
        $contador = new Contador();

        $contador->gerarQueryAcesso($tipo, ['idVeiculo'], $veiculo['idCadastro'], GranularidadeContator::Dia, [$idVeiculo], 'data');

        $contador = $contador->getDados();

        return new JsonModel( [
            'success' => '200',
            'data' =>  $contador,
        ]);

    }

    public function contatoAction()
    {
        $idVeiculo = $this->params('idVeiculo');
        
        $veiculoModel = $this->getContainer()->get(Veiculos::class);

        $veiculo = $veiculoModel->get($idVeiculo);
        
        $contador = new Contador();

        $contador->gerarQueryAcesso(TabelasContador::Contato, ['idVeiculo'], $veiculo['idCadastro'], GranularidadeContator::Dia, [$idVeiculo]);

        $contato = $contador->getDados();

        return new JsonModel( [
            'success' => '200',
            'data' =>  $contato,
        ]);

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
