<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Contador;
use AreaRestrita\Model\Veiculos;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

abstract class GranularidadeAcesso {
    const Dia = 'DATE(time)';
    const Semana = 'WEEK(DATE(time))';
    const Mes = 'MONTH(DATE(time))';
    const Ano = 'YEAR(DATE(time))';
}

class PainelController extends AbstractActionController
{
    private $athenaClient;
    private $dadosContador;
    private $veiculos;
    private $idCadastro;
    private $contador;

    public function indexAction()
    {

        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        $cadastro = $cadastrosModel->getCurrent();
        $idCadastro = $cadastro['idCadastro']; 
        
        $veiculosModel = $this->getContainer()->get(Veiculos::class); 

        $veiculos = $veiculosModel->getAll(compact('idCadastro'));
        
        $totalVeiculos = $veiculos['total'];

        $ignorarCondicoesBasicas = 0;

        $veiculosAtivos = $veiculosModel->getAll(compact('idCadastro', 'ignorarCondicoesBasicas'));

        $totalVeiculosAtivos = $veiculosAtivos['total'];

        $idsVeiculos = [];

        foreach($veiculos['data'] as $veiculo) {
            $idsVeiculos[] = $veiculo['idVeiculo'];
        }

        $this->contador = new Contador();
        $this->contador->gerarQueryAcesso(['idVeiculo'], $idCadastro, null, $idsVeiculos);

        $contadorPorVeiculo = $this->contador->getDados();

        //mescla as informações dos contadores, com os dados dos veículos
        //var_dump($contadorPorVeiculo);die;
        foreach($contadorPorVeiculo as $contador) {
            foreach($veiculos['data'] as  $k => $veiculo) {
                if($veiculo['idVeiculo'] == $contador['idVeiculo']) {
                    $veiculos['data'][$k]['contador'] = $contador['contador'];
                    break;
                }
            }
        }

        


        return new ViewModel(compact('totalVeiculos', 'totalVeiculosAtivos', 'veiculos'));

    }

    public function contadorPorMarcaAction()
    {
        //return $this->contadorPor(['marca']);
    }

    public function contadorPorModeloAction()
    {
        //return $this->contadorPor(['modelo']);
    }

    public function contadorPorCategoriaAction()
    {
        //return $this->contadorPor(['categoria']);
    }


    private function contadorPor($campos, $granularidade = GranularidadeAcesso::Dia)
    {
        try{
            $this->contador =  new Contador();
            $this->contador->gerarQueryAcesso($campos, null, $granularidade, null);
            $contador =  $this->contador->getDados();    
            
            return new JsonModel([
                'success' => 'SUCCESS',
                'data' =>  $contador,
            ]);

        }catch(\Error $e) {

            return new JsonModel([
                'success' => 'ERROR',
                'data' =>  $e->getMessage(),
            ]);
        }

    } 

    public function sumarioVeiculosAction()
    {
    }

    public function sumarioPropostasAction()
    {
    }

    public function sumarioEmailsRespondidosAction()
    {
    }

    public function meusVeiculosAction()
    {
    }

    public function detalheAnuncioAction()
    {
    }
}
