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

    /**
     * Retorna os dados do veículo que estiver na rota
     */
    public function getVeiculo()
    {
        $idVeiculo = (int) $this->params()->fromRoute('idVeiculo');

        if (!$idVeiculo) {
            return [];
        }
        $data = $this->getApiClient()->veiculosGet([
                'ignorarCondicoesBasicas' => true
                ], $idVeiculo)->getData();
        if ($data) {
            $data[0]['modeloCarro'] = $data[0]['idModelo'];
            return $data[0];
        }
        return [];
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
        $tipoVeiculo = $tipos[strtolower($this->params()->fromRoute('tipo'))];

        $dadosForm = new Veiculo\DadosForm();
        $dadosForm->setTipoVeiculo($tipoVeiculo);

        $veiculoDados = $this->getVeiculo();
        if ($veiculoDados) {
            $dadosForm->populateValues($veiculoDados);
            $dadosForm->setIsEdition(true);
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
                'idPlano' => 3,
            ];

            $data += $request->getPost()->toArray();

            $idVeiculo = isset($data['idVeiculo']) && $data['idVeiculo'] ? (int) $data['idVeiculo'] : null;

            if ($idVeiculo) {
                // Atualiza
                $data = array_diff_key($data, array_flip([
                    'idVeiculo',
                    'video',
                    'idAnuncioVeiculo',
                    'total',
                    'termo'
                ]));

                $keyRemap = [
                    'checkboxacessorios' => 'listaAcessorios',
                    'ocultarValorACombinar' => 'combinarPreco',
                ];
                foreach ($keyRemap as $from => $to) {
                    $data[$to] = $data[$from];
                    unset($data[$from]);
                }

                // Essa opção está obsoleta na regra de negócio
                $data['trocaVeiculoOpcoes']  = [];

                $res = $apiClient->veiculosPut($data, $idVeiculo);
            } else {
                // Cria
                $res = $apiClient->veiculosPost($data, $idVeiculo);
            }


            if ($res->status) {
                $this->response->setStatusCode($res->status);
            }

            return new JsonModel($res->json());
        }

        return new ViewModel([
            'formDadosVeiculos' => $dadosForm
        ]);
    }

    public function precoAction()
    {
        $precoForm = new Veiculo\PrecoForm();
        $data = $this->getVeiculo();
        $precoForm->populateValues($data);

        $this->layout()->setTemplate('none');
        return new ViewModel([
            'formPrecoVeiculo' => $precoForm
        ]);
    }

    public function maisInformacoesAction()
    {
        $maisInformacoesForm = new Veiculo\MaisInformacoesForm();
        $data = $this->getVeiculo();
        $maisInformacoesForm->populateValues($data);

        return new ViewModel([
            'formMaisInformacoesVeiculo' => $maisInformacoesForm
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

            $files = $moveUpload->move($request->getFiles()->fotos, true);

            $data = [
                'idTipo' => $dataPost->tipoCadastro,
                'idVeiculo' => $dataPost->idVeiculo,
                $apiClient::KEY_FILES => [
                    'fotos' => $files
                ],
            ];

            $res = $this->getApiClient()->veiculosFotosPost($data);
            foreach ($files as $file) {
                unlink($file);
            }
            return new JsonModel($res->json());
        }
        return new ViewModel();
    }

    public function videoAction()
    {
        $videoForm = new Veiculo\VideoForm();

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
}
