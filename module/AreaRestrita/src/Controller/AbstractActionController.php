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
     *  Se passado uma key específica, então retorna só este dado
     * 
     * @param string $key Chave do campo que será retornado
     * @return Array|null
     */
    public function getCadastro($key = false)
    {
        $data = $this
            ->getContainer()
            ->get(Cadastros::class)
            ->getCurrent();

        return $key ? $data[$key] : $data;
    }

    /**
     * Retorna os dados do veículo que estiver na rota
     */
    public function getVeiculo($cache = false)
    {
        $idVeiculo = (int) $this->params()->fromRoute('idVeiculo');

        if (!$idVeiculo) {
            return [];
        }
        $data = $this->getApiClient()->veiculosGet([
            'ignorarCondicoesBasicas' => true
            ], $idVeiculo, $cache);

        if ($data->status !== 200) {
            return false;
        }
        $data = $data->getData();

        if ($data) {
            $data[0]['modeloCarro'] = $data[0]['idModelo'];
            $data[0]['versao'] = $data[0]['caracteristica'];
            $data[0]['combustivel'] = $data[0]['idCombustivel'];
            $data[0]['portas'] = $data[0]['carroPortas'];
            $data[0]['motor'] = $data[0]['idMotor'];
            return $data[0];
        }
        return [];
    }
    /**
     * Retorna se o cadasto do usuário atual é revenda
     * @return bool
     */
    public function isRevenda()
    {
        return ((int) $this->getCadastro('tipoCadastro')) === Cadastros::TIPO_CADASTRO_REVENDA;
    }
}
