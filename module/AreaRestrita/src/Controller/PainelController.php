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

        $contador->gerarQueryAcesso(['idVeiculo'], $veiculo['idCadastro'], null, [$idVeiculo]);

        $cliques = $contador->getDados();

        $cliques = $cliques[1]['contador'] ?? 0;   

        return new ViewModel(compact('veiculo', 'cliques'));        
    }

    public function cliquesAction()
    {
        $idVeiculo = $this->params('idVeiculo');
        
        $veiculoModel = $this->getContainer()->get(Veiculos::class);

        $veiculo = $veiculoModel->get($idVeiculo);
        
        $contador = new Contador();

        $contador->gerarQueryAcesso(['idVeiculo'], $veiculo['idCadastro'], GranularidadeAcesso::Dia, [$idVeiculo]);

        $cliques = $contador->getDados();

        return new JsonModel( [
            'success' => '200',
            'data' =>  $cliques,
        ]);

    }
}
