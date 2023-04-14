<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use Zend\View\Model\JsonModel;

class ChatController extends AbstractActionController
{

    public function indexAction()
    {
        
    }

    public function messagesAction()
    {
        $idCadastro = $this->getCadastro('idCadastro');

        if ($this->getRequest()->isPost()) {
            return $this->sendMessages($idCadastro);
        }
        return $this->getMessages($idCadastro);
    }
    public function naoLidasAction()
    {
        $idCadastro = $this->getCadastro('idCadastro');

        if (!$idCadastro) {
            return new JsonModel();
        }

        $res = $this->getApiClient()->apiChatMensagensGet([
            'method' => 'countNaoLidas',
            'idCadastro' => $idCadastro,
        ]);

        return new JsonModel($res->getData());
    }
    protected function sendMessages($idCadastro)
    {
        /**
         * @todo verificar se o ID veículo é do cara mesmo
         */
        $data = $this->getRequest()->getPost()->toArray();
        $data['idCadastroRemetente'] = $idCadastro;

        $res = $this->getApiClient()->mensagensPost($data)->json();
        return new JsonModel($res);
    }

    protected function getMessages($idCadastro)
    {
        $params = [
            'idCadastro' => $idCadastro
        ];
        $idLastMessage = $this->params()->fromQuery('idLastMessage', false);
        if ($idLastMessage) {
            $params['maiorQue'] = $idLastMessage;
        }

        $apiClient = $this->getApiClient();
        $res = $apiClient->mensagensGet($params, null, !true);

        $data = $res->getData();
        $listChats = $data['listChats'];

        foreach ($listChats as &$cv) {
            $cv['idCadastro'] = (string) $cv['idCadastro'];
        }

        $this->addUserData($listChats);

        $data['listChats'] = $listChats;

        return new JsonModel($data);
    }

    protected function addUserData(&$listChats)
    {
        if (!$listChats) {
            return;
        }
        $firstkey = array_key_first($listChats);

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(true);

        $listChats[$firstkey]['meusDados'] = [
            'idCadastro' => $dadosCadastro['idCadastro'],
            'responsavelNome' => $dadosCadastro['responsavelNome'],
            'nomeFantasia' => $dadosCadastro['nomeFantasia'],
        ];
    }

    public function mensagensVeiculoAction()
    {
        $apiClient = $this->getApiClient();
        $idVeiculo = $this->params('idVeiculo');
        $params = $this->params()->fromQuery();

        $res = $apiClient->mensagensGet($params, $idVeiculo);
        $json = $res->json();
        if ($json) {
            echo '<pre>';
            var_export($json);
        } else {
            echo $res->getBody();
        }
        die;
        die;
    }
}
