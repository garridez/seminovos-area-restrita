<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;

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

    public function dispatchAction()
    {
        switch ($this->getRequest()->getMethod()) {

            case 'POST':
                return $this->create();
            case 'PUT':
                return $this->update();
            case 'PATCH':
                return $this->patch();
            case 'DELETE':
                return $this->delete();
            case 'GET':
            default:
                return $this->fetch();
        }
    }

    private function methodNotAllowed()
    {
        return new JsonModel([
            'status' => 405,
            'title' => 'Method Not Allowed',
            'detail' => sprintf('O metodo \'%s\' não implementado. Consulte a documentação', $this->getRequest()->getMethod())
        ]);
    }

    /**
     * POST
     * @return JsonModel
     */
    public function create()
    {
        return $this->methodNotAllowed();
    }

    /**
     * GET
     * @return JsonModel
     */
    public function fetch()
    {
        return $this->methodNotAllowed();
    }

    /**
     * PUT
     * @return JsonModel
     */
    public function update()
    {
        return $this->methodNotAllowed();
    }

    /**
     * PATCH
     * @return JsonModel
     */
    public function patch()
    {
        return $this->methodNotAllowed();
    }

    /**
     * DELETE
     * @return JsonModel
     */
    public function delete()
    {
        return $this->methodNotAllowed();
    }
}
