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
        $dadosForm = new Veiculo\DadosForm();

        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->request;
        if ($request->isPost()) {
            /* @var $identity Identity */
            $identity = $this->getContainer()->get(Identity::class);
            /* @var $apiClient ApiClient */
            $apiClient = $this->getContainer()->get(ApiClient::class);

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

            $data = [
                'tipoVeiculo' => $tipos[strtolower($this->params()->fromRoute('tipo'))],
                'idCadastro' => $identity->getIdentity(),
                'video' => '',
                'idPlano' => 3,
            ];

            $data += $request->getPost()->toArray();

            $idVeiculo = isset($data['idVeiculo']) && $data['idVeiculo'] ? $data['idVeiculo'] : null;

            $res = $apiClient->veiculosPost($data, $idVeiculo);

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

        $this->layout()->setTemplate('none');
        return new ViewModel([
            'formPrecoVeiculo' => $precoForm
        ]);
    }

    public function maisInformacoesAction()
    {
        $maisInformacoesForm = new Veiculo\MaisInformacoesForm();

        return new ViewModel([
            'formMaisInformacoesVeiculo' => $maisInformacoesForm
        ]);
    }

    public function fotosAction()
    {
        return new ViewModel();
    }

    public function videoAction()
    {
        $videoForm = new Veiculo\VideoForm();

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
