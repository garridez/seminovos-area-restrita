<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\MoveUpload;
use Zend\Mvc\MvcEvent;
use SnBH\Common\ServiceVeiculo;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;
use Zend\View\Model\JsonModel;

class VeiculoController extends AbstractActionController {

    public function create() {

        $request = $this->request;
        $idCadastro = $this->getIdCadastro();
       
        /* @var $apiClient ApiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);

        /**
         * @TODO
         * Criar um único lugar para recuperar o idTipo pelo nome
         */
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower($this->params()->fromPost('tipoVeiculo'))];
        }

        $data = [
            'tipoVeiculo' => $tipoVeiculo,
            'tipoCadastro' => 1,
            'idCadastro' => $idCadastro,
            'video' => '',
            'troca' => 4,
            'idPlano' => 5,
        ];

        $data += $request->getPost()->toArray();

        if (!isset($data['flagIpva'])) {
            $data['flagIpva'] = 0;
        }

        if (isset($data['versao']) && $data['versao'] == '-1') {
            $data['versao'] = '';
        }

        if (isset($data['kilometragem'])) {
            $data['kilometragem'] = str_replace('.', '', $data['kilometragem']);
        }

        if (isset($data['observacoes']) && $data['observacoes']) {
            // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
            $data['observacoes'] = substr($data['observacoes'], 0, 700);
        }

        // Se não for passado acessórios, envia "0" para apagar os existentes
        $data['listaAcessorios'] = $data['listaAcessorios'] ?? 0;

        $res = $apiClient->veiculosPost($data)->json();

        if ($res['status'] != 200) {
            return new JsonModel($res);
        }

        $idVeiculo = $res['data'][0]['idVeiculo'];

        return new JsonModel([
            'status' => 200,
            'detail' => 'Veículo inserido com sucesso!',
            'data' => ['idVeiculo' => $idVeiculo]
        ]);
    }
    
    public function update() {

        $idVeiculo = $this->params('id');

        parse_str(file_get_contents("php://input"), $_PUT);
        $data = $_PUT;
        $request = $this->request;
        
        /* @var $apiClient ApiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);

        if (!isset($data['flagIpva'])) {
            $data['flagIpva'] = 0;
        }

        if (isset($data['versao']) && $data['versao'] == '-1') {
            $data['versao'] = '';
        }

        if (isset($data['kilometragem'])) {
            $data['kilometragem'] = str_replace('.', '', $data['kilometragem']);
        }

        if (isset($data['observacoes']) && $data['observacoes']) {
            // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
            $data['observacoes'] = substr($data['observacoes'], 0, 700);
        }

        // Se não for passado acessórios, envia "0" para apagar os existentes
        $data['listaAcessorios'] = $data['listaAcessorios'] ?? 0;

        $res = $apiClient->veiculosPut($data, $idVeiculo)->json();

        if ($res['status'] != 200) {
            return new JsonModel($res);
        }

        return new JsonModel([
            'status' => 200,
            'detail' => 'Veículo atualizado com sucesso!',
        ]);
    }

    public function delete() {

        $idVeiculo = $this->params('id');

        /* @var $veiculosModel Veiculos */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

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
        
        if($dadosVeiculos['status'] !== 200){
            return new JsonModel($dadosVeiculos);
        }
        
        $dataJson = [
               'status' => 200,
               'detail' => $dadosVeiculos['detail']
            ];
        
        return new JsonModel($dataJson);

    }

}
