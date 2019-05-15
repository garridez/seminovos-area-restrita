<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\ServiceManager\ServiceManager;

class AbstractActionController extends ZendAbstractActionController
{

    public function getContainer(): ServiceManager
    {
        global $container;

        return $container;
    }

    public function getApiClient(): ApiClient
    {
        return $this->getContainer()->get(ApiClient::class);
    }

    /**
     * Verifica se o retorno da api é um erro.
     * Se sim, redireciona para  página de erro
     */
    public function checkApiError(Response $apiResponse)
    {
        if ($apiResponse->status !== 200) {
            throw new \Exception;
        }
    }

    /**
     * Retorna os dados de cadastro do usuário atual
     * @return Array|null
     */
    public function getCadastro()
    {
        return $this
                ->getContainer()
                ->get(Cadastros::class)
                ->getCurrent();
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
            ], $idVeiculo);

        if ($data->status !== 200) {
            return false;
        }
        $data = $data->getData();

        if ($data) {
            $data[0]['modeloCarro'] = $data[0]['idModelo'];
            return $data[0];
        }
        return [];
    }
}
