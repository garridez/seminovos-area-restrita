<?php

namespace AreaRestrita\Controller;

use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\ServiceManager\ServiceManager;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response;

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
}
