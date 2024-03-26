<?php

namespace SnBH\Importer\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Cidades;
use AreaRestrita\Model\Estados;
use Exception;
use Laminas\Mvc\Controller\AbstractActionController;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\StringFuncs;
use Throwable;

class IndexController extends AbstractActionController
{
    protected array $currentVeiculo;
    protected array $cidades;
    protected $idCadastro;
    protected $idVeiculo;

    protected $serviceManager;
    protected $apiClient;

    public function init()
    {
        //init memory limit
        ini_set('memory_limit', '256M');

        //init template
        $this->layout()->setTemplate('layout/blank');

        //init laminas utilities
        $this->apiClient      = $this->getApiClient();
        $this->serviceManager = $this->getEvent()->getApplication()->getServiceManager();
    }

    protected function getApiClient(): ApiClient
    {
        static $apiClient;

        if (!$apiClient) {
            $apiClient = $this->getEvent()->getApplication()->getServiceManager()->get(ApiClient::class);
        }

        return $apiClient;
    }

    public function indexAction()
    {
        $this->init();

        try {
            $mailtoNumber = 0;
            $importer     = [];

            foreach ($this->getVeiculos() as $veiculo) {
                $this->idCadastro      = null;
                $this->idVeiculo      = null;
                $this->currentVeiculo = $veiculo;

                $telefone = str_replace(['(', ')', '-', ' '], '', $this->currentVeiculo['telefone']);

                $cadastro = $this->apiClient->cadastrosGet([
                    'email' => IS_PROD ? $this->currentVeiculo['email'] : 'mailto' . ++$mailtoNumber . '@webmotors.com.br',
                ])->json();

                //se cadastro não existe
                if (empty($cadastro['data'])) {
                    $this->importarCadastro($mailtoNumber);
                } else {
                    $this->idCadastro = $cadastro['data']['0']['idCadastro'];
                }

                $responseImportarVeiculo = $this->importarVeiculo();

                //se der erro por placa já cadsatrada continua o foreach
                if (!$responseImportarVeiculo) {
                    continue;
                }

                if (count($this->currentVeiculo['fotos']) > 0) {
                    $this->importarFotos();
                }

                 $importer[] = [
                     'id' => $this->currentVeiculo['id'],
                     'id_cadastro' => $this->idCadastro,
                     'id_veiculo' => $this->idVeiculo,
                     'email' => $this->currentVeiculo['email'],
                 ];
            }
            echo json_encode([
                'importer' => $importer,
                'status'   => 200,
                'message'  => 'Foram importados ' . count($importer) . 'veículos.',
            ], JSON_PRETTY_PRINT);
        } catch (Throwable $th) {
            echo json_encode([
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Recupera veículos a serem importados
     */
    private function getVeiculos()
    {
        $jsonUrl = 'http://dashboard.seminovos.com.br:8088/api';
        $veiculos = json_decode(file_get_contents($jsonUrl), true);

        $veiculos = array_column($veiculos, null, 'id');

        return $veiculos;
    }

    /**
     * Importa Cadastro
     */
    private function importarCadastro($mailtoNumber = 0)
    {
        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $this->serviceManager->get(Cadastros::class);

        $estado = trim(explode('-', $this->currentVeiculo['cidade'])[1]);
        $estado = $this->getInfoEstado($estado);

        if (is_null($estado)) {
            $idCidade = null;
            $idEstado = null;
        } else {
            $cidade = trim(explode('-', $this->currentVeiculo['cidade'])[0]);
            $cidade = $this->getInfoCidade($estado['idEstado'], $cidade);

            $idCidade = $cidade['idCidade'];
            $idEstado = $estado['idEstado'];
            $estado   = $estado['estado'];
        }

        /**
         * @todo verifica cpf
         */

        $postDto = [
            'tipoCadastro'         => 2,
            'responsavelNome'      => $this->currentVeiculo['dono'],
            'telefone_1'           => $this->currentVeiculo['telefone'],
            'telefone_1_is_wpp'    => 0,
            'telefone_2'           => $this->currentVeiculo['telefone'],
            'telefone_2_is_wpp'    => 0,
            'email'                => IS_PROD ? $this->currentVeiculo['email'] : 'mailto' . $mailtoNumber . '@webmotors.com.br',
            'senha'                => 'snBH2023',
            'idCidade'             => $idCidade,
            'idEstado'             => $idEstado,
            'estado'               => $estado,
            'cadastroSimplificado' => true,
        ];

        $resPost = $cadastrosModel
                        ->post($postDto)
                        ->json();

        if (isset($resPost['status']) && $resPost['status'] != 200) {
            throw new Exception('Error importing registration ' . $postDto['email'] . ". " . $resPost['detail'], 1);
        }

        $this->idCadastro = $resPost['data'][0]['idCadastro'];
    }

    /**
     * Importa Veículos Com Acessórios
     *
     * @return bool | true se deu tudo certo /false se placa já cadastrada no sistema
     */
    private function importarVeiculo()
    {
        $combustivel = 1;

        if (isset($this->currentVeiculo['combustivel'])) {
            switch ($this->currentVeiculo['combustivel']) {
                case 'Álcool':
                    $this->currentVeiculo['combustivel'] = 1;
                    break;
                case 'Gasolina e álcool':
                    $this->currentVeiculo['combustivel'] = 2;
                    break;
                case 'Diesel':
                    $this->currentVeiculo['combustivel'] = 3;
                    break;
                case 'Gasolina':
                    $this->currentVeiculo['combustivel'] = 4;
                    break;
                case 'Gasolina e gás natural':
                    $this->currentVeiculo['combustivel'] = 5;
                    break;
                case 'Gás natural':
                    $this->currentVeiculo['combustivel'] = 6;
                    break;
                case 'Gasolina, álcool, gás natural e benzina':
                    $this->currentVeiculo['combustivel'] = 7;
                    break;
                case 'Gasolina e elétrico':
                    $this->currentVeiculo['combustivel'] = 8;
                    break;
                case 'Álcool e gás natural':
                    $this->currentVeiculo['combustivel'] = 9;
                    break;
                case 'Gasolina, álcool e gás natural':
                    $this->currentVeiculo['combustivel'] = 10;
                    break;
                case 'Elétrico':
                    $this->currentVeiculo['combustivel'] = 11;
                    break;
            }
        }

        $data = [
            'placa'           => $this->currentVeiculo['placa'],
            'tipoVeiculo'     => 1,
            'tipoCadastro'    => 2,
            'idCadastro'      => $this->idCadastro,
            'troca'           => 4,
            'idStatus'        => 2,
            'origem'          => 'Integrador',
            'kilometragem'    => $this->currentVeiculo['odometro'],
            'flagIpva'        => 0,
            'video'           => '',
            'versao'          => $this->currentVeiculo['versao'] ?? '',
            'observacoes'     => '',
            'marca'           => $this->currentVeiculo['idMarca'],
            'idModelo'        => $this->currentVeiculo['idModelo'],
            'anoFabricacao'   => $this->currentVeiculo['ano'],
            'anoModelo'       => $this->currentVeiculo['anoModelo'],
            'valor'           => $this->currentVeiculo['preco'],
            'idCombustivel'   => $combustivel,
            'cor'             => $this->currentVeiculo['cor'],
            'idPlano'         => 1,
            'listaAcessorios' => $this->getListaAcessorios(),
            'originVehicle'   => 'ImportWebMotors',
            'aceitaLigacao'   => 1,
            'aceitaChat'      => 1,
        ];

        $res = $this->apiClient->veiculosPost($data)->json();

        if ($res['status'] != 200) {
            if (isset($res['detail']) && $res['detail'] == "A placa informada: " . $this->currentVeiculo['placa'] . ", já está cadastrada em nosso sistema!") {
                return false;
            } else {
                $title = $res['title'] ?? '';
                $detail = $res['detail'] ?? '';
                $messages = implode(' - ', $res['messages'] ?? []);

                throw new Exception(
                    'Error importing vehicle ' . $this->currentVeiculo['id']
                        . " - $title - $detail - $messages",
                    1
                );
            }
        }

        $this->idVeiculo = $res['data'][0]['idVeiculo'];

        $res = $this->apiClient->veiculosPut([
            'idStatus' => 2,
        ], $this->idVeiculo);

        return true;
    }

    /**
     * Importa Fotos
     */
    private function importarFotos()
    {
        $files = [];
        $ordem = [];
        $ordemCount = 0;

        //download the image
        foreach ($this->currentVeiculo['fotos'] as $key => $foto) {
            // Caminho completo do arquivo de destino
            $filename = basename($foto);
            $files[] = $destinationFile = $this->getDestinationFile($this->idVeiculo) . $filename;
            $ordem[] = $ordemCount++;
            // Baixa a imagem e a salva localmente
            file_put_contents($destinationFile, file_get_contents($foto));
        }

        $data = [
            'idTipo' => 1,
            'idVeiculo' => $this->idVeiculo,
            'ordem' => $ordem,
        ];

        $data[$this->ApiClient::KEY_FILES] = [
            'fotos' => $files,
        ];

        $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();

        $res = $this->apiClient->veiculosPut([
            'idStatus' => 2,
        ], $this->idVeiculo);
    }

    /**
     * Gera um array contendo a lista de acessórios do veículo para posterior envio à API.
     */
    private function getListaAcessorios(): ?array
    {
        $listaAcessorios = [];

        if (isset($this->currentVeiculo['accessorios']) && !empty($this->currentVeiculo['accessorios'])) {
            foreach ($this->currentVeiculo['accessorios'] as $acessorio) {
                if (isset($acessorio['id'])) {
                    array_push($listaAcessorios, $acessorio['id']);
                }
            }
        }

        if ($this->currentVeiculo['transmissao'] == 'Automática') {
            array_push($listaAcessorios, 11);
        }

        return $listaAcessorios;
    }

    /**
     * Retorna informações de um estado
     */
    public function getInfoEstado(string $estado): array
    {
        $estadosModel = $this->serviceManager->get(Estados::class);
        $response = $estadosModel->get([
            'sigla' => $estado,
        ]);

        if (empty($response)) {
            return null;
        }

        return $response[0];
    }

    /**
     * Retorna um array com todas as cidades disponíveis
     *
     * @return array $cidades
     */
    private function getInfoCidade(int $estadoId, string $nomeCidade): array
    {
        $cidadesModel = $this->serviceManager->get(Cidades::class);
        $response = $cidadesModel->get([
            'idEstado' => $estadoId,
            'cidaNome' => StringFuncs::removerAcentos($nomeCidade),
        ]);

        if (empty($response)) {
            return null;
        }

        return $response[0];
    }

    private function getDestinationFile($idVeiculo)
    {
        $path = '';

        $path = $this->serviceManager->get('config')['dir']['upload'];
        $path .= DIRECTORY_SEPARATOR . '/' . $idVeiculo . '/';
        if (!file_exists($path)) {
            mkdir($path);
        }

        return $path;
    }
}
