<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Service\Identity;
use AreaRestritaAnuncio\Form\Veiculo;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\MoveUpload;
use SnBH\Common\Helper\StringFuncs;
use SnBH\Common\Helper\VeiculoClearCache;

class DadosVeiculoController extends AbstractActionController
{
    public function onDispatch(MvcEvent $e)
    {
        $res = parent::onDispatch($e);
        if ($res instanceof ViewModel) {
            $res->setTerminal(true);
        }
        return $res;
    }

    public function dadosAction()
    {
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
            $tipoVeiculo = $tipos[strtolower((string) $this->params()->fromRoute('tipo'))];
        }

        $dadosForm = new Veiculo\DadosForm();
        $dadosForm->setTipoVeiculo($tipoVeiculo);
        $dadosForm->setCombustivel($tipoVeiculo);

        $cambio = null;
        $veiculoDados = $this->getVeiculo(5);
        if ($veiculoDados) {
            $dadosForm->populateValues($veiculoDados);
            $dadosForm->setIsEdition(true);
            $cambio = (int) $veiculoDados['idAcessorio'];
        }

        //libera edição para revendas
        if (isset($veiculoDados['cadastro']['tipoCadastro']) && $veiculoDados['cadastro']['tipoCadastro'] === '1') {
            $dadosForm->setIsEdition(false);
        }

        $request = $this->request;
        if ($request->isPost()) {
            /** @var Identity $identity */
            $identity = $this->getContainer()->get(Identity::class);
            /** @var ApiClient $apiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $data = [
                'tipoVeiculo' => $tipoVeiculo,
                'idCadastro' => $identity->getIdentity(),
                'video' => '',
                'origem' => 'Area Restrita',
            ];

            $data += $request->getPost()->toArray();

            if (!empty($data['outraVersao'])) {
                $data['versao'] = $data['outraVersao'];
            }

            if (isset($data['versao']) && $data['versao'] == '-1') {
                $data['versao'] = '';
            }

            $idVeiculo = isset($data['idVeiculo']) && $data['idVeiculo'] ? (int) $data['idVeiculo'] : null;

            if (isset($data['kilometragem'])) {
                $data['kilometragem'] = str_replace('.', '', (string) $data['kilometragem']);
            }

            if (isset($data['observacoes']) && $data['observacoes']) {
                // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
                $auxTexto = str_replace("\r\n", "", (string) StringFuncs::removerAcentos($data['observacoes']));
                if (strlen($auxTexto) > 700) {
                    $data['observacoes'] = mb_substr((string) $data['observacoes'], 0, 710, 'UTF8');
                }
            }

            $keyRemap = [
                'checkboxacessorios' => 'listaAcessorios',
                'ocultarValorACombinar' => 'combinarPreco',
            ];

            foreach ($keyRemap as $from => $to) {
                if (isset($data[$from])) {
                    $data[$to] = $data[$from];
                    unset($data[$from]);
                }
            }

            if (isset($data['listaAcessorios'])) {
                $data['listaAcessorios'] = array_filter($data['listaAcessorios'], function ($value) {
                    return $value !== '';
                });
            }

            // Se não for passado acessórios, envia "0" para apagar os existentes
            $data['listaAcessorios'] ??= 0;

            if ($idVeiculo) {
                // Atualiza
                $data = array_diff_key($data, array_flip([
                    'idVeiculo',
                    'video',
                    'idAnuncioVeiculo',
                    'total',
                    'termo',
                ]));

                if (isset($data['acao']) && $data['acao'] == 'publicar') {
                    unset($data['listaAcessorios']);
                }

                /*if(isset($data['tipoCad']) && $data['tipoCad'] === '2') {
                unset($data['flagLeilao' ]);
                }*/

                // Essa opção está obsoleta na regra de negócio
                $data['trocaVeiculoOpcoes'] = [];

                $res = $apiClient->veiculosPut($data, $idVeiculo);
            } else {
                // Cria
                if ($this->isRevenda()) {
                    // Revenda cria o anúncio por padrão no Básico
                    $data['idPlano'] = 5;
                } else {
                    // Particular cria o anúncio por padrão no grátis
                    $data['idPlano'] = 1;

                    if (!isset($data['motor'])) {
                        $data['motor'] = 0;
                    }
                }
                $res = $apiClient->veiculosPost($data, $idVeiculo);
            }

            if (!is_null($idVeiculo)) {
                VeiculoClearCache::clearCache($idVeiculo);

                // Limpa o cache do middleware
                $this->getContainer()->get(Veiculos::class)->clearIsOwnerCache();
            }

            if ($res->status) {
                $this->response->setStatusCode($res->status);
            }
            return new JsonModel($res->json());
        }

        $checkedLeilao = empty($veiculoDados) ? false : $veiculoDados['flagLeilao'];

        return new ViewModel([
            'checkedLeilao' => $checkedLeilao,
            'formDadosVeiculos' => $dadosForm,
            'cambio' => $cambio,
        ]);
    }

    public function opcionaisAction()
    {
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3,
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower((string) $this->params()->fromRoute('tipo'))];
        }

        $dadosForm = new Veiculo\DadosForm();
        $dadosForm->setTipoVeiculo($tipoVeiculo);
        $dadosForm->setCombustivel($tipoVeiculo);

        $cambio = null;
        $veiculoDados = $this->getVeiculo(false);
        if ($veiculoDados) {
            $dadosForm->populateValues($veiculoDados);
            $dadosForm->setIsEdition(true);
            $cambio = (int) $veiculoDados['idAcessorio'];
        }

        //libera edição para revendas
        if (isset($veiculoDados['cadastro']['tipoCadastro']) && $veiculoDados['cadastro']['tipoCadastro'] === '1') {
            $dadosForm->setIsEdition(false);
        }

        return new ViewModel([
            'formDadosVeiculos' => $dadosForm,
            'cambio' => $cambio,
        ]);
    }

    public function precoAction()
    {
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3,
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower((string) $this->params()->fromRoute('tipo'))];
        }

        $precoForm = new Veiculo\PrecoForm();
        $data = $this->getVeiculo(false);
        $precoForm->populateValues($data);

        $this->layout()->setTemplate('none');

        $checkedCombinarValor = empty($data) ? true : $data['combinarValor'];
        $checkedExibirKm = empty($data) ? true : $data['flag_km'];
        $checkedFlagFinanciar = empty($data) ? true : $data['flagFinanciar'] ?? 0;

        if ($data['idVeiculo'] ?? false) {
            $precoForm->setIsEdition();
        }

        return new ViewModel([
            'formPrecoVeiculo' => $precoForm,
            'tipoVeiculo' => $tipoVeiculo,
            'checkedExibirKm' => $checkedExibirKm,
            'checkedFlagFinanciar' => $checkedFlagFinanciar,
            'checkedCombinarValor' => $checkedCombinarValor,
            'data' => $data,
        ]);
    }

    public function maisInformacoesAction()
    {
        $maisInformacoesForm = new Veiculo\MaisInformacoesForm();
        $data = $this->getVeiculo(false);
        $maisInformacoesForm->populateValues($data);

        $checkedTermo = !empty($data);

        $checkedProposta = empty($data) ? false : $data['aceitaProposta'];

        $checkedLigacao = empty($data) ? false : $data['aceitaLigacao'];

        $checkedChat = empty($data) ? false : $data['aceitaChat'];

        return new ViewModel([
            'formMaisInformacoesVeiculo' => $maisInformacoesForm,
            'checkedTermo' => $checkedTermo,
            'checkedProposta' => $checkedProposta,
            'checkedLigacao' => $checkedLigacao,
            'checkedChat' => $checkedChat,
        ]);
    }

    public function fotosAction()
    {
        ini_set('memory_limit', '1G');
        ini_set('post_max_size', '700M');

        $request = $this->request;
        if ($request->isPost()) {
            $dataPost = $request->getPost();
            $apiClient = $this->getApiClient();
            $tempDir = $this->getContainer()->get('config')['dir']['upload'];
            $tempDir .= DIRECTORY_SEPARATOR . $dataPost->idVeiculo;
            if (!file_exists($tempDir)) {
                mkdir($tempDir);
            }
            $moveUpload = new MoveUpload([
                'target' => $tempDir,
                'overwrite' => true,
                'randomize' => true,
                'use_upload_name' => true,
                'use_upload_extension' => true,
            ]);
            $auxReordem = [];
            // Delete
            if ($dataPost->fotosToDelete) {
                $resDelete = $this->getApiClient()->veiculosFotosDelete([
                    'listaFotos' => $dataPost->fotosToDelete,
                ])->json();
                $dataPost->reordem ??= [];
                $auxReordem = array_diff($dataPost->reordem, $dataPost->fotosToDelete);
                //$dataPost->reordem = array_filter(array_merge(array(0), array_values($auxReordem)));
            }
            $fotos = $request->getFiles()->fotos;
            // Upload
            if ($fotos) {
                $data = [
                    'idTipo' => $dataPost->tipoCadastro,
                    'idVeiculo' => $dataPost->idVeiculo,
                    'ordem' => $dataPost->ordem,
                    'rotacionar' => $dataPost->rotacionarNovasFotos,
                ];

                $files = $moveUpload->move($request->getFiles()->fotos, true);
                $data[$apiClient::KEY_FILES] = [
                    'fotos' => $files,
                ];

                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();
                foreach ($files as $file) {
                    unlink($file);
                }
                if (isset($resUpload['data']['fotosInseridas'])) {
                    $itemsCount = is_countable($resUpload['data']['fotosInseridas']) ? count($resUpload['data']['fotosInseridas']) : 0;
                    for ($i = 0; $i < $itemsCount; $i++) {
                        $auxReordem[$dataPost->ordem[$i]] = $resUpload['data']['fotosInseridas'][$i];
                    }
                }
            }

            if ($dataPost->reordem) {
                $resReordem = $this->getApiClient()->veiculosFotosPut([
                    'reordem' => $dataPost->reordem,
                    'metadata' => [
                        'idVeiculo' => $dataPost->idVeiculo,
                    ],
                ])->json();
            }
            VeiculoClearCache::clearCache($dataPost->idVeiculo);

            $dataJson = [
                'status' => 200,
                'resUpload' => $resUpload ?? [],
            ];
            if (isset($resUpload) && $resUpload['status'] !== 200) {
                $dataJson = $resUpload;
            }
            if (isset($resDelete) && $resDelete['status'] !== 200) {
                $dataJson = $resDelete;
            }
            if (isset($resReordem) && $resReordem['status'] !== 200) {
                $dataJson = $resReordem;
            }

            return new JsonModel($dataJson);
        }
        $fotos = [];
        $dadosVeiculo = $this->getVeiculo(5);
        if ($dadosVeiculo) {
            $fotos = $dadosVeiculo['fotos'];
        }
        return new ViewModel([
            'fotos' => $fotos,
            'existeVeiculo' => isset($dadosVeiculo['idVeiculo']) && $dadosVeiculo['idVeiculo'],
        ]);
    }

    public function videoAction()
    {
        $videoForm = new Veiculo\VideoForm();
        $data = $this->getVeiculo(5);
        $videoForm->populateValues($data);

        $request = $this->request;
        if ($request->isPost()) {
            $post = $request->getPost();
            $apiClient = $this->getApiClient();

            $res = $apiClient->veiculosPut([
                'video' => $post->video,
            ], $post->idVeiculo);

            return new JsonModel($res->json());
        }

        return new ViewModel([
            'formVideoVeiculo' => $videoForm,
        ]);
    }

    public function salvarDadosAction()
    {
        $dadosForm = new Veiculo\DadosForm();

        $request = $this->getRequest();

        //        var_dump($request);exit;

        if ($request->isPost()) {
            $post = $request->getPost();
            $dadosForm->setData($post);

            if ($dadosForm->isValid()) {
                /** @var Cadastros $veiculosModel */
                $veiculosModel = $this->getContainer()->get(Veiculos::class);

                /** @var ApiClient $apiClient */
                $data = $dadosForm->getData();

                $data['anoFabricacao'] = $data['selectanofabricacao'];
                $data['anoModelo'] = $data['selectanomodelo'];
                $data['carroPortas'] = $data['selectportas'];
                $data['cor'] = $data['selectcor'];
                $data['idCombustivel'] = $data['selectcombustivel'];
                $data['idStatus'] = 3; // Cadastrando
                $data['tipoVeiculo'] = 1;
                $data['idCadastro'] = 253536;
                $data['veiculo_zero_km'] = 0;

                // campos que não existentes na tabela
                unset($data['selectanofabricacao']);
                unset($data['selectanomodelo']);
                unset($data['selectportas']);
                unset($data['selectcor']);
                unset($data['selectcombustivel']);
                unset($data['submit']);
                unset($data['checkboxacessorios']);

                // var_dump($data);
                // exit;

                $resPost = $veiculosModel->post($data);

                $this->checkApiError($resPost);

                echo json_encode($resPost->json());
                die;
            } else {
                echo 'dados invalidos';
                var_dump($dadosForm->getMessages());
                die;
            }
        } else {
            return new ViewModel([
                'formDadosVeiculos' => $dadosForm,
            ]);
        }
    }

    /**
     * Verifica se a placa está disponível para cadastro
     * Retorna TRUE se a placa estiver disponível
     * Retorna FALSE se a placa estiver indisponível
     */
    public function placaDisponivelAction()
    {
        $statusPermitidos = [
            /*1, // aguardando pagamento
                        3, // cadastrando */
            7, // removido
            8, // vendido
        ];
        $placa = $this->params()->fromRoute('placa', false);
        if (!$placa) {
            return new JsonModel(['status' => 405, 'detail' => 'Placa não informada']);
        }
        /** @var ApiClient $apiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);


        $veiculo = $apiClient->veiculosGet([
            "ignorarCondicoesBasicas" => 1,
            "flagPlaca" => 1,
        ], $placa, false)->json();

        $placaDisponivel = false;
        if (isset($veiculo) && $veiculo['status'] != 200) {
            $placaDisponivel = true;
        } elseif (!isset($veiculo['data'][0]['idVeiculo'])) {
            $placaDisponivel = true;
        } else {
            $placaDisponivel = in_array($veiculo['data'][0]['idStatus'], $statusPermitidos);
        }

        //$placaDisponivel = true;
        return new JsonModel([
            'status' => 200,
            'placaDisponivel' => $placaDisponivel,
            'historicoCarro' => $veiculo['data'][0]['historicoCarro'],
        ]);
    }

    public function getVersaoAction()
    {
        $request = $this->request;

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();

            /** @var ApiClient $apiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $versao = $apiClient->versaoGet($post)->json();

            echo json_encode($versao);

            die;
        }
    }

    public function gratisAction()
    {
        $data = [];
        $request = $this->getRequest();

        if ($request->isPost()) {
            /** @var ApiClient $apiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $post = $request->getPost();

            $idVeiculo = $post['idVeiculo'];

            $result = $apiClient->veiculosGet([
                'ignorarCondicoesBasicas' => true,
            ], (int) $idVeiculo, 5);

            $veiculo = $result->getData();

            $arrayStatusAltera = ['1', '3', '10'];

            if ($veiculo[0]['idPlano'] == 1 && $post['idPlano'] == 1 && !in_array($veiculo[0]['idStatus'], $arrayStatusAltera)) {
                return new JsonModel(['status' => 405, 'detail' => 'Não é possível utilizar o plano grátis mais de uma vez', 'title' => 'Selecione outro Plano']);
            }

            if (in_array($veiculo[0]['idStatus'], $arrayStatusAltera)) {
                $data['tipoCadastro'] = $post['tipoCadastro'];
                $data['idPlano'] = $post['idPlano'];
                $data['idStatus'] = 6;
                $data['idAnuncioVeiculo'] = $post['idAnuncioVeiculo'];

                $result = $apiClient->veiculosPut($data, $idVeiculo);
            }
            return new JsonModel($result->json());
        }
    }

    public function clearCacheAction()
    {
        $idVeiculo = $this->params()->fromRoute('idVeiculo', false);
        if (!$idVeiculo) {
            return;
        }
        VeiculoClearCache::clearCache($idVeiculo);
        die;
    }
}
