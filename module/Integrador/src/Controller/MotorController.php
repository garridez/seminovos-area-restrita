<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class MotorController extends AbstractActionController {

    public function fetch() {

        $request = $this->request;
        $idCadastro = $this->getIdCadastro();

        $res = $this->getApiClient()->motores([
                'tipo' => 1
                ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }

}
