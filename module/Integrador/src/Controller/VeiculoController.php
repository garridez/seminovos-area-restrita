<?php

namespace SnBH\Integrador\Controller;

use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;
use Laminas\View\Model\JsonModel;
use SnBH\ApiClient\Client as ApiClient;

class VeiculoController extends AbstractActionController
{
    public function create()
    {
        $request = $this->request;
        $idCadastro = $this->getIdCadastro();

        /** @var ApiClient $apiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);

        $plano = $this->getApiClient()->planosGet([
            'idCadastro' => $idCadastro,
            'tipoCadastro' => 1,
        ], 'anuncios')
                ->getData()[0];

        /**
         * @todo
         * Criar um único lugar para recuperar o idTipo pelo nome
         */
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3,
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower((string) $this->params()->fromPost('tipoVeiculo'))];
        }

        $data = [
            'tipoVeiculo' => $tipoVeiculo,
            'tipoCadastro' => 1,
            'idCadastro' => $idCadastro,
            'troca' => 4,
            'idStatus' => 2,
            'origem' => 'Integrador',
        ];

        $data += $request->getPost()->toArray();
        file_put_contents('data/logs/' . date('Y-m-d-') . $idCadastro . '.log', json_encode($data) . "\n");

        $data['idPlano'] ??= 5;

        if ($data['idPlano'] == 5 && $plano['totalBasico'] <= $plano['totalBasicoPublicado']) {
            return new JsonModel(['status' => 405, 'detail' => 'Excedido número de veículos do plano Básico']);
        } elseif ($data['idPlano'] == 2 && $plano['totalTurbo'] <= $plano['totalTurboPublicados']) {
            return new JsonModel(['status' => 405, 'detail' => 'Excedido número de veículos do plano Turbo']);
        } elseif ($data['idPlano'] == 3 && $plano['totalNitro'] <= $plano['totalNitroPublicados']) {
            return new JsonModel(['status' => 405, 'detail' => 'Excedido número de veículos do plano Nitro']);
        }

        if (!isset($data['flagIpva'])) {
            $data['flagIpva'] = 0;
        }

        if (!isset($data['video'])) {
            $data['video'] = '';
        }

        if (isset($data['versao']) && $data['versao'] == '-1') {
            $data['versao'] = '';
        }

        if (isset($data['kilometragem'])) {
            $data['kilometragem'] = str_replace('.', '', (string) $data['kilometragem']);
        }

        if (isset($data['observacoes']) && $data['observacoes']) {
            // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
            $data['observacoes'] = substr((string) $data['observacoes'], 0, 700);
        }

        // Se não for passado acessórios, envia "0" para apagar os existentes
        $data['listaAcessorios'] ??= 0;

        $res = $apiClient->veiculosPost($data)->json();

        if ($res['status'] != 200) {
            return new JsonModel($res);
        }

        $idVeiculo = $res['data'][0]['idVeiculo'];

        return new JsonModel([
            'status' => 200,
            'detail' => 'Veículo inserido com sucesso!',
            'data' => ['idVeiculo' => $idVeiculo],
        ]);
    }

    public function update()
    {
        $idVeiculo = $this->params('id');

        parse_str(file_get_contents("php://input"), $_PUT);
        $data = $_PUT;
        $request = $this->request;

        /** @var ApiClient $apiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);

        if (!isset($data['flagIpva'])) {
            $data['flagIpva'] = 0;
        }

        if (isset($data['versao']) && $data['versao'] == '-1') {
            $data['versao'] = '';
        }

        if (isset($data['portas']) && $data['portas']) {
            $data['carroPortas'] = $data['portas'];
        }

        if (isset($data['combustivel']) && $data['combustivel']) {
            $data['idCombustivel'] = $data['combustivel'];
        }

        if (isset($data['kilometragem'])) {
            $data['kilometragem'] = str_replace('.', '', $data['kilometragem']);
        }

        if (isset($data['observacoes']) && $data['observacoes']) {
            // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
            $data['observacoes'] = mb_substr($data['observacoes'], 0, 700);
        }

        // Se não for passado acessórios, envia "0" para apagar os existentes
        $data['listaAcessorios'] ??= 0;

        $res = $apiClient->veiculosPut($data, $idVeiculo)->json();

        if ($res['status'] != 200) {
            return new JsonModel($res);
        }

        return new JsonModel([
            'status' => 200,
            'detail' => 'Veículo atualizado com sucesso!',
        ]);
    }

    public function delete()
    {
        $idVeiculo = $this->params('id');

        /** @var Veiculos $veiculosModel */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        /** @var VeiculosFotos $veiculosFotosModel */
        $veiculosFotosModel = $this->getContainer()->get(VeiculosFotos::class);

        // Busca os dados das fotos do veiculo
        $dadosVeiculoFotos = $veiculosFotosModel->get($idVeiculo);

        if ($dadosVeiculoFotos) {
            $listaFotos = [];
            foreach ($dadosVeiculoFotos as $key => $dado) {
                $listaFotos[] = $dado['idFoto'];
            }
            // deletar fotos do servidor
            $veiculosFotosModel->delete([
                'listaFotos' => $listaFotos,
            ]);
        }

        // quando o tipoCadastro for 1 (revenda) a API já irá deletar registro das tabelas veiculos, anuncios_veiculos e veiculos_fotos
        $dadosVeiculos = $veiculosModel->delete($idVeiculo);

        if (isset($dadosVeiculos['status']) && $dadosVeiculos['status'] !== 200) {
            return new JsonModel($dadosVeiculos);
        }

        $dataJson = [
            'status' => 200,
            'detail' => $dadosVeiculos['detail'],
        ];

        return new JsonModel($dataJson);
    }

    public function fetch()
    {
        $placa = $this->params()->fromQuery('placa');

        if (empty($placa) || strlen($placa) < 5) {
            return new JsonModel([
                'status' => 401,
                'detail' => 'Falta parâmetro necessário',
            ]);
        }

        /** @var ApiClient $apiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);

        $res = $apiClient->veiculosGet([
            "ignorarCondicoesBasicas" => 1,
            "flagPlaca" => 1,
        ], $placa, false)->json();

        return new JsonModel($res);
    }
}
