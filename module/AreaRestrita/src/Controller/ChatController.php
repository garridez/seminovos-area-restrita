<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use Laminas\View\Model\JsonModel;

class ChatController extends AbstractActionController
{
    /**
     * @return JsonModel
     */
    public function messagesAction()
    {
        $idCadastro = $this->getCadastro('idCadastro');

        if ($this->getRequest()->isPost()) {
            return $this->sendMessages($idCadastro);
        }
        return $this->getMessages($idCadastro);
    }

    /**
     * @return JsonModel
     */
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

    /**
     * @param string|int $idCadastro
     */
    protected function sendMessages($idCadastro): JsonModel
    {
        /**
         * @todo verificar se o ID veículo é do cara mesmo
         */
        $data = $this->getRequest()->getPost()->toArray();
        $data['idCadastroRemetente'] = $idCadastro;

        $res = $this->getApiClient()->mensagensPost($data)->json();
        return new JsonModel($res);
    }

    /**
     * @param string|int $idCadastro
     */
    protected function getMessages($idCadastro): JsonModel
    {
        $params = [
            'idCadastro' => $idCadastro,
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

    /**
     * @param array $listChats
     */
    protected function addUserData(&$listChats): void
    {
        if (!$listChats) {
            return;
        }
        $firstkey = array_key_first($listChats);

        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(true);

        if ($dadosCadastro === false) {
            return;
        }

        $listChats[$firstkey]['meusDados'] = [
            'idCadastro' => $dadosCadastro['idCadastro'],
            'responsavelNome' => $dadosCadastro['responsavelNome'],
            'nomeFantasia' => $dadosCadastro['nomeFantasia'],
        ];
    }

    public function mensagensVeiculoAction(): never
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
    }
}
