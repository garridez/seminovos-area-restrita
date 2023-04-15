<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;

class CidadesController extends AbstractActionController {

    public function fetch()
    {
        $id = $this->params()->fromQuery('idEstado');

        $res = $this->getApiClient()->cidades([
                'idEstado' => $id
                ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }

}
