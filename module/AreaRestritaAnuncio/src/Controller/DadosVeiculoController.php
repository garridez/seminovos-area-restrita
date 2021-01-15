<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestritaAnuncio\Form\Veiculo;
use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Service\Identity;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\MoveUpload;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

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
         * @TODO
         * Criar um único lugar para recuperar o idTipo pelo nome
         *
         */
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower($this->params()->fromRoute('tipo'))];
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
        if(isset($veiculoDados['cadastro']['tipoCadastro']) && $veiculoDados['cadastro']['tipoCadastro'] === '1') {
            $dadosForm->setIsEdition(false);
        }

        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->request;
        if ($request->isPost()) {
            /* @var $identity Identity */
            $identity = $this->getContainer()->get(Identity::class);
            /* @var $apiClient ApiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $data = [
                'tipoVeiculo' => $tipoVeiculo,
                'idCadastro' => $identity->getIdentity(),
                'video' => '',
            ];

            $data += $request->getPost()->toArray();

            if(!empty($data['outraVersao'])){
                $data['versao'] = $data['outraVersao'];
            }

            if(isset($data['versao']) && $data['versao'] == '-1'){
                $data['versao'] = '';
            }

            $idVeiculo = isset($data['idVeiculo']) && $data['idVeiculo'] ? (int) $data['idVeiculo'] : null;

            if (isset($data['kilometragem'])) {
                $data['kilometragem'] = str_replace('.', '', $data['kilometragem']);
            }

            if (isset($data['observacoes']) && $data['observacoes']) {
                // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
                $data['observacoes'] = substr($data['observacoes'], 0, 700);
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
            // Se não for passado acessórios, envia "0" para apagar os existentes
            $data['listaAcessorios'] = $data['listaAcessorios'] ?? 0;

            $data['listaAcessorios'] = array_filter($data['listaAcessorios'], function($value){
                return $value !== '';
            });

            if ($idVeiculo) {
                // Atualiza
                $data = array_diff_key($data, array_flip([
                    'idVeiculo',
                    'video',
                    'idAnuncioVeiculo',
                    'total',
                    'termo'
                ]));

                if(isset($data['acao']) && $data['acao'] == 'publicar'){
                    unset($data['listaAcessorios']);
                }

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

                    if(!isset($data['motor'])){
                        $data['motor'] = 0;
                    }
                }
                $res = $apiClient->veiculosPost($data, $idVeiculo);
            }
            // Limpa o cache do middleware
            $this->getContainer()->get(Veiculos::class)->clearIsOwnerCache();

            if ($res->status) {
                $this->response->setStatusCode($res->status);
            }
            return new JsonModel($res->json());
        }

        return new ViewModel([
            'formDadosVeiculos' => $dadosForm,
            'cambio' => $cambio
        ]);
    }

    public function opcionaisAction()
    {
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3
        ];
        $tipoVeiculo = (int) $this->params()->fromPost('tipoVeiculo', 0);
        if ($tipoVeiculo === 0) {
            $tipoVeiculo = $tipos[strtolower($this->params()->fromRoute('tipo'))];
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
        if(isset($veiculoDados['cadastro']['tipoCadastro']) && $veiculoDados['cadastro']['tipoCadastro'] === '1') {
            $dadosForm->setIsEdition(false);
        }

        return new ViewModel([
            'formDadosVeiculos' => $dadosForm,
            'cambio' => $cambio
        ]);
    }

    public function precoAction()
    {
        $precoForm = new Veiculo\PrecoForm();
        $data = $this->getVeiculo(5);
        $precoForm->populateValues($data);

        $this->layout()->setTemplate('none');

        if($data['idVeiculo'] ?? false) {
            $precoForm->setIsEdition();
        }

        return new ViewModel([
            'formPrecoVeiculo' => $precoForm
        ]);
    }

    public function maisInformacoesAction()
    {
        $maisInformacoesForm = new Veiculo\MaisInformacoesForm();
        $data = $this->getVeiculo(5);
        $maisInformacoesForm->populateValues($data);

        $checkedTermo = (empty($data) ? false : true);

        $checkedProposta = (empty($data) ?  false : $data['aceitaProposta']);

        $checkedLigacao = (empty($data) ?  false : $data['aceitaLigacao']);

        return new ViewModel([
            'formMaisInformacoesVeiculo' => $maisInformacoesForm,
            'checkedTermo' => $checkedTermo,
            'checkedProposta' => $checkedProposta,
            'checkedLigacao' => $checkedLigacao,
        ]);
    }

    public function fotosAction()
    {
        /* @var $request \Zend\Http\PhpEnvironment\Request */
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
                        'listaFotos' => $dataPost->fotosToDelete
                    ])->json();

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
                    'fotos' => $files
                ];

                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();
                foreach ($files as $file) {
                    unlink($file);
                }

                for($i = 0; $i < sizeof($resUpload['data']['fotosInseridas']); $i++){
                    $auxReordem[$dataPost->ordem[$i]] = $resUpload['data']['fotosInseridas'][$i];
                }

            }

            if ($dataPost->fotosToDelete) {
                ksort($auxReordem);
                $dataPost->reordem = array_filter(array_merge(array(0), array_values($auxReordem)));
            }

            if ($dataPost->reordem) {
                $resReordem = $this->getApiClient()->veiculosFotosPut([
                    'reordem' => $dataPost->reordem,
                    'metadata' => [
                        'idVeiculo' => $dataPost->idVeiculo
                    ],
                ])->json();
            }

            $dataJson = [
                'status' => 200
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
            'existeVeiculo' => isset($dadosVeiculo['idVeiculo']) &&  $dadosVeiculo['idVeiculo']
        ]);
    }

    public function videoAction()
    {
        $videoForm = new Veiculo\VideoForm();
        $data = $this->getVeiculo(5);
        $videoForm->populateValues($data);

        /* @var $request \Zend\Http\PhpEnvironment\Request */
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
            'formVideoVeiculo' => $videoForm
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

                /* @var $veiculosModel Cadastros */
                $veiculosModel = $this->getContainer()->get(Veiculos::class);

                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $dadosForm->getData();

                $data['anoFabricacao'] = $data['selectanofabricacao'];
                $data['anoModelo'] = $data['selectanomodelo'];
                $data['carroPortas'] = $data['selectportas'];
                $data['cor'] = $data['selectcor'];
                $data['idCombustivel'] = $data['selectcombustivel'];
                $data['idStatus'] = 3; #Cadastrando
                $data['tipoVeiculo'] = 1;
                $data['idCadastro'] = 253536;
                $data['veiculo_zero_km'] = 0;

                #campos que não existentes na tabela
                unset($data['selectanofabricacao']);
                unset($data['selectanomodelo']);
                unset($data['selectportas']);
                unset($data['selectcor']);
                unset($data['selectcombustivel']);
                unset($data['submit']);
                unset($data['checkboxacessorios']);

                var_dump($data);
                exit;

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

            $placa = $this->params('placa');
            $tipoVeiculo = 1;

//            $dadosForm->get('placa')->setValue($placa);
//            $dadosForm->get('idTipo')->setValue($tipoVeiculo);

            return new ViewModel([
                'formDadosVeiculos' => $dadosForm
            ]);
        }
//        return new ViewModel();
    }
    /**
     * Verifica se a placa está disponível para cadastro
     * Retorna TRUE se a placa estiver disponível
     * Retorna FALSE se a placa estiver indisponível
     */
    public function placaDisponivelAction(){
        $statusPermitidos = [
                            1, // aguardando pagamento
                            3, // cadastrando
                            7, // removido
                            8, // vendido
                        ];
        $placa = $this->params()->fromRoute('placa',false);
        if(!$placa){
            return new JsonModel(['status'=> 405, 'detail'=> 'Placa não informada']);
        }
        /* @var $apiClient ApiClient */
        $apiClient = $this->getContainer()->get(ApiClient::class);
        $veiculo = $apiClient->veiculosGet(
        [
            "ignorarCondicoesBasicas" => 1,
            "flagPlaca" => 1
        ], $placa, false)->json();

        $placaDisponivel = false;
        if($veiculo['status']!= 200){
            $placaDisponivel =  true;
        }else if(!isset($veiculo['data'][0]['idVeiculo'])){
            $placaDisponivel =  true;
        }else{
            $placaDisponivel = in_array($veiculo['data'][0]['idStatus'],$statusPermitidos);
        }
//$placaDisponivel = true;
        return new JsonModel( [
            'status' => 200,
            'placaDisponivel' => $placaDisponivel,
            'historicoCarro' => $veiculo['data'][0]['historicoCarro']
        ]);
    }

    public function getVersaoAction()
    {
        $request = $this->request;

        if ($request->isPost()) {

            $post = $request->getPost()->toArray();;

            /* @var $apiClient ApiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $versao = $apiClient->versaoGet($post)->json();

            echo json_encode($versao);

            die;

        }
    }

    public function gratisAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            /* @var $apiClient ApiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

            $post = $request->getPost();

            $idVeiculo = $post['idVeiculo'];

            $result = $apiClient->veiculosGet([
                'ignorarCondicoesBasicas' => true,
            ], (int)$idVeiculo, 5);

            $veiculo = $result->getData();

            $arrayStatusAltera = ['1', '3', '10'];

            if($veiculo[0]['idPlano'] == 1 && $post['idPlano']==1 && !in_array($veiculo[0]['idStatus'], $arrayStatusAltera)){
                return new JsonModel(['status' => 405, 'detail' =>'Não é possível utilizar o plano grátis mais de uma vez', 'title'=>'Selecione outro Plano']);
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
}
