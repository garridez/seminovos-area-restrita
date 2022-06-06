<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class EstadosController extends AbstractActionController {

    public function fetch()
    {
        $id = $this->params()->fromQuery('idEstado');

        $res = $this->getApiClient()->estados([
                ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }

}
